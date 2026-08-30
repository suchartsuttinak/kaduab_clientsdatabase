@extends('admin_client.admin_client')

@section('content')
@php
    $thaiDate = function ($value) {
        if (!$value) {
            return '-';
        }

        try {
            $date = $value instanceof \Carbon\Carbon
                ? $value
                : \Carbon\Carbon::parse($value);

            return $date->format('d/m/') . ($date->year + 543);
        } catch (\Throwable $e) {
            return '-';
        }
    };

    $clientFullName = trim(
        ($client->prefix ?? '')
        . ($client->first_name ?? '')
        . ' '
        . ($client->last_name ?? '')
    );

    if ($clientFullName === '') {
        $clientFullName = $client->fullname ?? $client->name ?? '-';
    }

    $processLabels = [
        'group_therapy' => 'กลุ่มบำบัด',
        'family_therapy' => 'ครอบครัวบำบัด',
        'psychotherapy_counseling' => 'จิตบำบัด/ให้คำปรึกษา',
        'behavior_therapy' => 'พฤติกรรมบำบัด',
        'referred_treatment' => 'ส่งต่อเข้ารับการบำบัด',
    ];

    $riskLabels = [
        'none' => 'ไม่พบความเสี่ยง',
        'low' => 'ความเสี่ยงต่ำ',
        'moderate' => 'ความเสี่ยงปานกลาง',
        'high' => 'ความเสี่ยงสูง',
    ];

    $statusLabels = [
        'ongoing' => 'อยู่ระหว่างดำเนินการ',
        'goal_met' => 'บรรลุเป้าหมาย',
    ];

    $rounds = $observe->referralRounds ?? collect();
    $latestRound = $rounds->last();

    $referralFollowup = $observe->followups
        ->filter(fn ($item) => ($item->status ?? 'ongoing') === 'referred')
        ->last();

    $referralDate = $referralFollowup->followup_date ?? $observe->date;
    $referralSource = $referralFollowup
        ? 'การติดตามครั้งที่ ' . ($referralFollowup->followup_count ?: '-')
        : 'รอบบันทึกแรก';

    $latestStatus = $latestRound->status ?? null;
    $latestRisk = $latestRound->risk_level ?? null;
@endphp

<div class="referral-report-page" id="referralAssistanceReport">
    <div class="referral-report-toolbar no-print">
        <div>
            <h1>รายงานการช่วยเหลือหลังส่งต่อ</h1>
            <p>สรุปลำดับการช่วยเหลือ ความเสี่ยง ผลลัพธ์ และแผนดำเนินการต่อ</p>
        </div>

        <div class="referral-report-actions">
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer"></i> พิมพ์รายงาน
            </button>
            <a href="{{ route('observe.edit', $observe->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> กลับหน้าดำเนินการ
            </a>
        </div>
    </div>

    <article class="referral-report-paper">
        <header class="referral-report-header">
            <div class="referral-report-eyebrow">รายงานการดำเนินงานรายกรณี</div>
            <h2>การช่วยเหลือหลังส่งต่อ</h2>
            <div class="referral-report-client">{{ $clientFullName }}</div>
        </header>

        <div class="referral-report-confidential">
            <i class="bi bi-shield-lock"></i>
            <span>ข้อมูลจำกัดสิทธิ์เฉพาะนักสังคมสงเคราะห์ ผู้บริหาร และ Admin</span>
        </div>

        <section class="referral-report-summary" aria-label="สรุปสถานะ">
            <div class="referral-summary-item">
                <span>วันที่ส่งต่อข้อมูล</span>
                <strong>{{ $thaiDate($referralDate) }}</strong>
            </div>
            <div class="referral-summary-item">
                <span>จำนวนรอบช่วยเหลือ</span>
                <strong>{{ $rounds->count() }} รอบ</strong>
            </div>
            <div class="referral-summary-item">
                <span>สถานะล่าสุด</span>
                <strong>{{ $latestStatus ? ($statusLabels[$latestStatus] ?? '-') : 'รอดำเนินการ' }}</strong>
            </div>
            <div class="referral-summary-item">
                <span>ระดับความเสี่ยงล่าสุด</span>
                <strong>{{ $latestRisk ? ($riskLabels[$latestRisk] ?? '-') : '-' }}</strong>
            </div>
        </section>

        <section class="referral-report-section">
            <div class="referral-section-heading">
                <span class="referral-section-no">01</span>
                <div>
                    <h3>ข้อมูลอ้างอิงการส่งต่อ</h3>
                    <p>ข้อมูลต้นทางสำหรับทำความเข้าใจบริบทของการช่วยเหลือ</p>
                </div>
            </div>

            <dl class="referral-info-grid">
                <div>
                    <dt>ผู้รับบริการ</dt>
                    <dd>{{ $clientFullName }}</dd>
                </div>
                <div>
                    <dt>สภาพปัญหา</dt>
                    <dd>{{ $observe->misbehavior->misbehavior_name ?? '-' }}</dd>
                </div>
                <div>
                    <dt>วันที่เกิดเหตุ</dt>
                    <dd>{{ $thaiDate($observe->date) }}</dd>
                </div>
                <div>
                    <dt>จุดที่ส่งต่อ</dt>
                    <dd>{{ $referralSource }}</dd>
                </div>
            </dl>

            <div class="referral-context-list">
                <div class="referral-context-row">
                    <div class="referral-context-label">พฤติกรรมที่พบเห็น</div>
                    <div class="referral-context-value">{!! nl2br(e($observe->behavior ?: '-')) !!}</div>
                </div>
                <div class="referral-context-row">
                    <div class="referral-context-label">สาเหตุ / ปัจจัยที่เกี่ยวข้อง</div>
                    <div class="referral-context-value">{!! nl2br(e($observe->cause ?: '-')) !!}</div>
                </div>
            </div>
        </section>

        <section class="referral-report-section">
            <div class="referral-section-heading">
                <span class="referral-section-no">02</span>
                <div>
                    <h3>ลำดับการช่วยเหลือหลังส่งต่อ</h3>
                    <p>เรียงตามรอบการดำเนินงานเพื่อให้เห็นความต่อเนื่องของการช่วยเหลือ</p>
                </div>
            </div>

            <div class="referral-round-list">
                @forelse($rounds as $round)
                    <section class="referral-round-card">
                        <div class="referral-round-header">
                            <div class="referral-round-title">
                                <span>รอบที่ {{ $round->round_no ?: $loop->iteration }}</span>
                                <strong>{{ $processLabels[$round->assistance_process] ?? '-' }}</strong>
                            </div>
                            <div class="referral-round-date">{{ $thaiDate($round->action_date) }}</div>
                        </div>

                        <div class="referral-round-meta">
                            <div>
                                <span>สถานะ</span>
                                <strong>{{ $statusLabels[$round->status ?? 'ongoing'] ?? '-' }}</strong>
                            </div>
                            <div>
                                <span>ระดับความเสี่ยง</span>
                                <strong>{{ $riskLabels[$round->risk_level ?? 'none'] ?? '-' }}</strong>
                            </div>
                            <div>
                                <span>ผู้ดำเนินการ</span>
                                <strong>{{ $round->recorder_name ?: '-' }}</strong>
                            </div>
                        </div>

                        <div class="referral-narrative-grid">
                            <div class="referral-narrative-block">
                                <h4>แนวทางแก้ไข</h4>
                                <div>{!! nl2br(e($round->solution ?: '-')) !!}</div>
                            </div>
                            <div class="referral-narrative-block">
                                <h4>ผลลัพธ์</h4>
                                <div>{!! nl2br(e($round->result ?: '-')) !!}</div>
                            </div>
                        </div>

                        @if($round->risk_detail)
                            <div class="referral-detail-line">
                                <span>รายละเอียดความเสี่ยง</span>
                                <div>{!! nl2br(e($round->risk_detail)) !!}</div>
                            </div>
                        @endif

                        @if(($round->status ?? 'ongoing') === 'ongoing')
                            <div class="referral-next-plan">
                                <div>
                                    <span>วันนัดหมายครั้งต่อไป</span>
                                    <strong>{{ $thaiDate($round->next_appointment_date) }}</strong>
                                </div>
                                <div>
                                    <span>ประเด็นที่จะดำเนินการต่อในรอบถัดไป</span>
                                    <strong>{!! nl2br(e($round->followup_focus ?: '-')) !!}</strong>
                                </div>
                            </div>
                        @else
                            <div class="referral-goal-complete">
                                <i class="bi bi-check-circle"></i>
                                <span>บรรลุเป้าหมายและสิ้นสุดการช่วยเหลือหลังส่งต่อ</span>
                            </div>
                        @endif
                    </section>
                @empty
                    <div class="referral-empty-state">
                        <strong>ยังไม่มีการดำเนินการหลังส่งต่อ</strong>
                        <span>สถานะปัจจุบันคือส่งต่อข้อมูลและรอผู้รับผิดชอบเริ่มดำเนินการ</span>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="referral-report-section referral-current-section">
            <div class="referral-section-heading">
                <span class="referral-section-no">03</span>
                <div>
                    <h3>สถานะปัจจุบันและการดำเนินการต่อ</h3>
                    <p>ใช้สำหรับทบทวนสถานการณ์ล่าสุดและวางแผนรอบถัดไป</p>
                </div>
            </div>

            @if($latestRound)
                <dl class="referral-current-grid">
                    <div>
                        <dt>ดำเนินการล่าสุด</dt>
                        <dd>รอบที่ {{ $latestRound->round_no }} · {{ $thaiDate($latestRound->action_date) }}</dd>
                    </div>
                    <div>
                        <dt>กระบวนการช่วยเหลือล่าสุด</dt>
                        <dd>{{ $processLabels[$latestRound->assistance_process] ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt>สถานะ</dt>
                        <dd>{{ $statusLabels[$latestRound->status ?? 'ongoing'] ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt>ระดับความเสี่ยง</dt>
                        <dd>{{ $riskLabels[$latestRound->risk_level ?? 'none'] ?? '-' }}</dd>
                    </div>
                </dl>

                @if(($latestRound->status ?? 'ongoing') === 'ongoing')
                    <div class="referral-current-plan">
                        <div>
                            <span>นัดหมายครั้งต่อไป</span>
                            <strong>{{ $thaiDate($latestRound->next_appointment_date) }}</strong>
                        </div>
                        <div>
                            <span>ประเด็นดำเนินการต่อ</span>
                            <strong>{!! nl2br(e($latestRound->followup_focus ?: '-')) !!}</strong>
                        </div>
                    </div>
                @else
                    <div class="referral-current-complete">
                        <strong>ผลสรุป: บรรลุเป้าหมาย</strong>
                        <span>การช่วยเหลือหลังส่งต่อสิ้นสุดแล้ว และไม่มีรอบดำเนินการถัดไป</span>
                    </div>
                @endif
            @else
                <div class="referral-current-complete referral-current-pending">
                    <strong>รอดำเนินการหลังส่งต่อ</strong>
                    <span>ยังไม่มีการบันทึกรอบการช่วยเหลือโดยผู้รับผิดชอบ</span>
                </div>
            @endif
        </section>

        <footer class="referral-report-footer">
            <span>พิมพ์จากระบบเมื่อ {{ $thaiDate(now('Asia/Bangkok')) }}</span>
            <span>ผู้รับบริการ: {{ $clientFullName }}</span>
        </footer>
    </article>
</div>

<style>
.referral-report-page {
    padding: 22px 14px 36px;
    background: #f5f7fa;
    color: #172033;
    font-family: inherit;
}

.referral-report-toolbar {
    width: min(930px, 100%);
    margin: 0 auto 16px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}

.referral-report-toolbar h1 {
    margin: 0 0 4px;
    font-size: 1.38rem;
    font-weight: 800;
}

.referral-report-toolbar p {
    margin: 0;
    color: #667085;
    font-size: .9rem;
}

.referral-report-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.referral-report-paper {
    width: min(930px, 100%);
    margin: 0 auto;
    padding: 34px 38px;
    background: #fff;
    border: 1px solid #e1e6ed;
    border-radius: 14px;
    box-shadow: 0 16px 42px rgba(15, 23, 42, .06);
}

.referral-report-header {
    padding-bottom: 18px;
    text-align: center;
    border-bottom: 2px solid #202939;
}

.referral-report-eyebrow {
    margin-bottom: 4px;
    color: #667085;
    font-size: .78rem;
    font-weight: 700;
    letter-spacing: .05em;
}

.referral-report-header h2 {
    margin: 0;
    font-size: 1.62rem;
    font-weight: 800;
    color: #101828;
}

.referral-report-client {
    margin-top: 6px;
    color: #344054;
    font-size: 1rem;
    font-weight: 700;
}

.referral-report-confidential {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    margin: 12px 0 0;
    color: #667085;
    font-size: .76rem;
}

.referral-report-summary {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    margin: 18px 0 0;
    border-top: 1px solid #d0d5dd;
    border-bottom: 1px solid #d0d5dd;
}

.referral-summary-item {
    padding: 11px 13px;
    border-right: 1px solid #eaecf0;
}

.referral-summary-item:last-child {
    border-right: 0;
}

.referral-summary-item span,
.referral-summary-item strong {
    display: block;
}

.referral-summary-item span {
    margin-bottom: 3px;
    color: #667085;
    font-size: .75rem;
    font-weight: 700;
}

.referral-summary-item strong {
    color: #101828;
    font-size: .9rem;
    line-height: 1.45;
}

.referral-report-section {
    margin-top: 26px;
}

.referral-section-heading {
    display: flex;
    align-items: flex-start;
    gap: 11px;
    margin-bottom: 12px;
}

.referral-section-no {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 34px;
    height: 28px;
    border: 1px solid #98a2b3;
    border-radius: 6px;
    color: #344054;
    font-size: .75rem;
    font-weight: 800;
}

.referral-section-heading h3 {
    margin: 0;
    color: #101828;
    font-size: 1rem;
    font-weight: 800;
}

.referral-section-heading p {
    margin: 2px 0 0;
    color: #667085;
    font-size: .78rem;
}

.referral-info-grid,
.referral-current-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    margin: 0;
    border: 1px solid #d0d5dd;
}

.referral-info-grid > div,
.referral-current-grid > div {
    padding: 10px 12px;
    border-right: 1px solid #eaecf0;
    border-bottom: 1px solid #eaecf0;
}

.referral-info-grid > div:nth-child(2n),
.referral-current-grid > div:nth-child(2n) {
    border-right: 0;
}

.referral-info-grid > div:nth-last-child(-n+2),
.referral-current-grid > div:nth-last-child(-n+2) {
    border-bottom: 0;
}

.referral-info-grid dt,
.referral-current-grid dt {
    margin: 0 0 3px;
    color: #667085;
    font-size: .75rem;
    font-weight: 700;
}

.referral-info-grid dd,
.referral-current-grid dd {
    margin: 0;
    color: #101828;
    font-size: .9rem;
    font-weight: 700;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

.referral-context-list {
    border: 1px solid #d0d5dd;
    border-top: 0;
}

.referral-context-row {
    display: grid;
    grid-template-columns: 170px minmax(0, 1fr);
    border-bottom: 1px solid #eaecf0;
}

.referral-context-row:last-child {
    border-bottom: 0;
}

.referral-context-label {
    padding: 10px 12px;
    background: #f9fafb;
    color: #344054;
    font-size: .8rem;
    font-weight: 800;
}

.referral-context-value {
    padding: 10px 12px;
    color: #101828;
    font-size: .88rem;
    line-height: 1.65;
    overflow-wrap: anywhere;
}

.referral-round-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.referral-round-card {
    border: 1px solid #d0d5dd;
    border-radius: 9px;
    overflow: hidden;
    background: #fff;
}

.referral-round-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    padding: 11px 13px;
    border-bottom: 1px solid #eaecf0;
    background: #f9fafb;
}

.referral-round-title {
    display: flex;
    align-items: baseline;
    gap: 9px;
    min-width: 0;
}

.referral-round-title span {
    color: #475467;
    font-size: .76rem;
    font-weight: 800;
    white-space: nowrap;
}

.referral-round-title strong {
    color: #101828;
    font-size: .93rem;
    font-weight: 800;
}

.referral-round-date {
    color: #475467;
    font-size: .8rem;
    font-weight: 700;
    white-space: nowrap;
}

.referral-round-meta {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    border-bottom: 1px solid #eaecf0;
}

.referral-round-meta > div {
    padding: 8px 12px;
    border-right: 1px solid #eaecf0;
}

.referral-round-meta > div:last-child {
    border-right: 0;
}

.referral-round-meta span,
.referral-round-meta strong {
    display: block;
}

.referral-round-meta span {
    margin-bottom: 2px;
    color: #667085;
    font-size: .72rem;
    font-weight: 700;
}

.referral-round-meta strong {
    color: #101828;
    font-size: .82rem;
    font-weight: 700;
    line-height: 1.4;
}

.referral-narrative-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

.referral-narrative-block {
    min-width: 0;
    padding: 12px 13px;
}

.referral-narrative-block + .referral-narrative-block {
    border-left: 1px solid #eaecf0;
}

.referral-narrative-block h4 {
    margin: 0 0 5px;
    color: #344054;
    font-size: .77rem;
    font-weight: 800;
}

.referral-narrative-block div {
    color: #101828;
    font-size: .86rem;
    line-height: 1.65;
    overflow-wrap: anywhere;
}

.referral-detail-line {
    padding: 10px 13px;
    border-top: 1px solid #eaecf0;
    background: #fff;
}

.referral-detail-line span {
    display: block;
    margin-bottom: 3px;
    color: #667085;
    font-size: .73rem;
    font-weight: 800;
}

.referral-detail-line div {
    color: #101828;
    font-size: .84rem;
    line-height: 1.6;
}

.referral-next-plan,
.referral-current-plan {
    display: grid;
    grid-template-columns: 180px minmax(0, 1fr);
    border-top: 1px solid #d0d5dd;
    background: #fcfcfd;
}

.referral-next-plan > div,
.referral-current-plan > div {
    padding: 10px 13px;
}

.referral-next-plan > div + div,
.referral-current-plan > div + div {
    border-left: 1px solid #eaecf0;
}

.referral-next-plan span,
.referral-next-plan strong,
.referral-current-plan span,
.referral-current-plan strong {
    display: block;
}

.referral-next-plan span,
.referral-current-plan span {
    margin-bottom: 3px;
    color: #667085;
    font-size: .72rem;
    font-weight: 800;
}

.referral-next-plan strong,
.referral-current-plan strong {
    color: #101828;
    font-size: .84rem;
    line-height: 1.55;
    overflow-wrap: anywhere;
}

.referral-goal-complete {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 9px 13px;
    border-top: 1px solid #d0d5dd;
    color: #344054;
    font-size: .8rem;
    font-weight: 700;
    background: #f9fafb;
}

.referral-empty-state {
    padding: 28px 20px;
    border: 1px dashed #98a2b3;
    border-radius: 9px;
    text-align: center;
    background: #fcfcfd;
}

.referral-empty-state strong,
.referral-empty-state span {
    display: block;
}

.referral-empty-state strong {
    margin-bottom: 4px;
    color: #101828;
    font-size: .92rem;
}

.referral-empty-state span {
    color: #667085;
    font-size: .8rem;
}

.referral-current-plan {
    margin-top: -1px;
    border: 1px solid #d0d5dd;
}

.referral-current-complete {
    padding: 12px 14px;
    border: 1px solid #d0d5dd;
    background: #f9fafb;
}

.referral-current-complete strong,
.referral-current-complete span {
    display: block;
}

.referral-current-complete strong {
    color: #101828;
    font-size: .9rem;
}

.referral-current-complete span {
    margin-top: 3px;
    color: #667085;
    font-size: .8rem;
}

.referral-current-pending {
    border-style: dashed;
}

.referral-report-footer {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
    margin-top: 28px;
    padding-top: 10px;
    border-top: 1px solid #d0d5dd;
    color: #667085;
    font-size: .72rem;
}

@media (max-width: 767.98px) {
    .referral-report-page {
        padding: 12px 7px 24px;
    }

    .referral-report-paper {
        padding: 22px 14px;
        border-radius: 10px;
    }

    .referral-report-summary,
    .referral-info-grid,
    .referral-current-grid,
    .referral-round-meta,
    .referral-narrative-grid,
    .referral-next-plan,
    .referral-current-plan {
        grid-template-columns: 1fr;
    }

    .referral-summary-item,
    .referral-info-grid > div,
    .referral-current-grid > div,
    .referral-round-meta > div {
        border-right: 0;
        border-bottom: 1px solid #eaecf0;
    }

    .referral-summary-item:last-child,
    .referral-info-grid > div:last-child,
    .referral-current-grid > div:last-child,
    .referral-round-meta > div:last-child {
        border-bottom: 0;
    }

    .referral-context-row {
        grid-template-columns: 1fr;
    }

    .referral-context-label {
        border-bottom: 1px solid #eaecf0;
    }

    .referral-narrative-block + .referral-narrative-block,
    .referral-next-plan > div + div,
    .referral-current-plan > div + div {
        border-left: 0;
        border-top: 1px solid #eaecf0;
    }

    .referral-round-header,
    .referral-round-title,
    .referral-report-footer {
        flex-direction: column;
    }
}

@page {
    size: A4 portrait;
    margin: 12mm;
}

@media print {
    html,
    body {
        width: 100% !important;
        min-width: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .content-page,
    .main-content,
    .page-content,
    .content,
    .wrapper,
    .container,
    .container-fluid {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
        position: static !important;
        transform: none !important;
    }

    body * {
        visibility: hidden !important;
    }

    #referralAssistanceReport,
    #referralAssistanceReport * {
        visibility: visible !important;
    }

    #referralAssistanceReport {
        position: absolute !important;
        inset: 0 auto auto 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        color: #000 !important;
        font-family: "Noto Sans Thai", "Leelawadee UI", Tahoma, sans-serif !important;
    }

    .no-print {
        display: none !important;
    }

    .referral-report-paper {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    .referral-report-header {
        padding-bottom: 7px !important;
        border-bottom: 1.5px solid #000 !important;
    }

    .referral-report-eyebrow,
    .referral-report-confidential,
    .referral-section-heading p,
    .referral-summary-item span,
    .referral-info-grid dt,
    .referral-current-grid dt,
    .referral-round-meta span,
    .referral-next-plan span,
    .referral-current-plan span,
    .referral-detail-line span,
    .referral-report-footer {
        color: #000 !important;
    }

    .referral-report-eyebrow,
    .referral-report-confidential,
    .referral-report-footer {
        font-size: 7.3pt !important;
    }

    .referral-report-header h2 {
        font-size: 15pt !important;
    }

    .referral-report-client {
        margin-top: 2px !important;
        font-size: 9.5pt !important;
    }

    .referral-report-confidential {
        margin-top: 6px !important;
    }

    .referral-report-summary {
        margin-top: 8px !important;
        border-color: #000 !important;
    }

    .referral-summary-item {
        padding: 5px 6px !important;
        border-color: #000 !important;
    }

    .referral-summary-item span {
        font-size: 7pt !important;
    }

    .referral-summary-item strong {
        font-size: 8pt !important;
        line-height: 1.3 !important;
    }

    .referral-report-section {
        margin-top: 10px !important;
    }

    .referral-section-heading {
        gap: 6px !important;
        margin-bottom: 5px !important;
    }

    .referral-section-no {
        flex-basis: 27px !important;
        height: 22px !important;
        border-color: #000 !important;
        border-radius: 3px !important;
        font-size: 7pt !important;
    }

    .referral-section-heading h3 {
        font-size: 9pt !important;
    }

    .referral-section-heading p {
        margin-top: 0 !important;
        font-size: 6.8pt !important;
    }

    .referral-info-grid,
    .referral-current-grid,
    .referral-context-list,
    .referral-round-card,
    .referral-current-plan,
    .referral-current-complete {
        border-color: #000 !important;
    }

    .referral-info-grid > div,
    .referral-current-grid > div,
    .referral-context-row,
    .referral-context-label,
    .referral-round-header,
    .referral-round-meta,
    .referral-round-meta > div,
    .referral-narrative-block + .referral-narrative-block,
    .referral-detail-line,
    .referral-next-plan,
    .referral-next-plan > div + div,
    .referral-current-plan > div + div {
        border-color: #000 !important;
    }

    .referral-info-grid > div,
    .referral-current-grid > div,
    .referral-context-label,
    .referral-context-value,
    .referral-round-header,
    .referral-round-meta > div,
    .referral-narrative-block,
    .referral-detail-line,
    .referral-next-plan > div,
    .referral-current-plan > div,
    .referral-current-complete {
        padding: 5px 6px !important;
    }

    .referral-info-grid dt,
    .referral-current-grid dt,
    .referral-context-label,
    .referral-round-title span,
    .referral-round-meta span,
    .referral-narrative-block h4,
    .referral-detail-line span,
    .referral-next-plan span,
    .referral-current-plan span {
        font-size: 7pt !important;
    }

    .referral-info-grid dd,
    .referral-current-grid dd,
    .referral-context-value,
    .referral-round-title strong,
    .referral-round-date,
    .referral-round-meta strong,
    .referral-narrative-block div,
    .referral-detail-line div,
    .referral-next-plan strong,
    .referral-current-plan strong,
    .referral-current-complete strong,
    .referral-current-complete span {
        font-size: 8pt !important;
        line-height: 1.35 !important;
    }

    .referral-round-list {
        gap: 7px !important;
    }

    .referral-round-card {
        border-radius: 0 !important;
        break-inside: avoid-page;
        page-break-inside: avoid;
    }

    .referral-round-header,
    .referral-context-label,
    .referral-next-plan,
    .referral-goal-complete,
    .referral-current-complete {
        background: #f2f2f2 !important;
    }

    .referral-goal-complete {
        padding: 5px 6px !important;
        border-color: #000 !important;
        font-size: 7.5pt !important;
    }

    .referral-report-footer {
        margin-top: 10px !important;
        padding-top: 5px !important;
        border-color: #000 !important;
    }
}
</style>
@endsection
