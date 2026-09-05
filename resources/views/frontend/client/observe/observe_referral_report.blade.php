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

            <a href="{{ ($fromReferralCenter ?? false)

                    ? route('observe.referrals.show', $observe->id)

                    : route('observe.edit', $observe->id) }}" class="btn btn-outline-secondary">

                <i class="bi bi-arrow-left"></i> กลับหน้าดำเนินการ

            </a>

        </div>

    </div>

    <article class="referral-report-paper">

      <header class="referral-report-header">
    <h2>รายงานการช่วยเหลือหลังส่งต่อ</h2>

    @php
        $displayPrefix = trim((string) ($client->prefix ?? ''));

        try {
            $age = !empty($client->birth_date)
                ? \Carbon\Carbon::parse($client->birth_date)->age
                : null;
        } catch (\Throwable $e) {
            $age = null;
        }

        $gender = strtolower(trim((string) ($client->gender ?? '')));

        /*
         * ใช้คำนำหน้าตามอายุ
         * อายุต่ำกว่า 15 ปี  = เด็กชาย / เด็กหญิง
         * อายุ 15 ปีขึ้นไป   = นาย / นางสาว
         */
        if ($age !== null) {
            $isMale = in_array($gender, [
                'ชาย', 'male', 'm', '1'
            ], true);

            $isFemale = in_array($gender, [
                'หญิง', 'female', 'f', '2'
            ], true);

            if ($age < 15) {
                if ($isMale) {
                    $displayPrefix = 'เด็กชาย';
                } elseif ($isFemale) {
                    $displayPrefix = 'เด็กหญิง';
                }
            } else {
                if ($isMale) {
                    $displayPrefix = 'นาย';
                } elseif ($isFemale) {
                    $displayPrefix = 'นางสาว';
                }
            }
        }

        $displayFullName = trim(
            $displayPrefix . ' ' .
            ($client->first_name ?? '') . ' ' .
            ($client->last_name ?? '')
        );

        if ($displayFullName === '') {
            $displayFullName = $clientFullName ?? '-';
        }
    @endphp

    <div class="referral-report-client">
        <span class="client-label">ชื่อ–สกุล</span>
        <span class="client-name">{{ $displayFullName }}</span>
    </div>
</header>

        <div class="referral-report-confidential">

            <i class="bi bi-shield-lock"></i>

            <span>ข้อมูลนี้เปิดให้เฉพาะบัญชีที่ได้รับสิทธิ์ศูนย์รับเคสพฤติกรรม</span>

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

                    <h3>1. ข้อมูลอ้างอิงการส่งต่อ</h3>

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

                    <h3>2. ลำดับการช่วยเหลือหลังส่งต่อ</h3>

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

                    <h3>3. สถานะปัจจุบันและการดำเนินการต่อ</h3>

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
    padding: 24px 16px 40px;
    background: #f1f2f4;
    color: #111;
    font-family: "Noto Sans Thai", "Leelawadee UI", Tahoma, sans-serif;
}

.referral-report-toolbar {
    width: min(900px, 100%);
    margin: 0 auto 14px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}

.referral-report-toolbar h1 {
    margin: 0 0 3px;
    font-size: 1.28rem;
    font-weight: 700;
    color: #111;
}

.referral-report-toolbar p {
    margin: 0;
    color: #666;
    font-size: .86rem;
}

.referral-report-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.referral-report-paper {
    width: min(900px, 100%);
    margin: 0 auto;
    padding: 38px 46px 32px;
    background: #fff;
    border: 0;
    border-radius: 0;
    box-shadow: none;
}

.referral-report-header {
    padding-bottom: 12px;
    border-bottom: 1.5px solid #222;
}

.referral-report-header h2 {
    margin: 0;
    text-align: center;
    font-size: 1.45rem;
    font-weight: 700;
    color: #111;
}

.referral-report-client {
    margin-top: 14px;
    text-align: left;
    font-size: .95rem;
    line-height: 1.6;
    color: #111;
}

.referral-report-client .client-label {
    margin-right: 8px;
    font-weight: 600;
}

.referral-report-client .client-name {
    font-weight: 400;
}

.referral-report-eyebrow {
    margin-bottom: 2px;
    color: #333;
    font-size: .82rem;
    font-weight: 400;
    letter-spacing: 0;
}

.referral-report-header h2 {
    margin: 0;
    color: #000;
    font-size: 1.48rem;
    font-weight: 700;
    line-height: 1.35;
}

.referral-report-client {
    margin-top: 14px;
    text-align: left;
    font-size: .95rem;
    color: #111;
    font-weight: 400;
}

.referral-report-client .client-label {
    font-weight: 700;
    margin-right: 8px;
}

.referral-report-client .client-name {
    font-weight: 400;
}

.referral-report-confidential {
    display: flex;
    justify-content: flex-end;
    margin: 7px 0 0;
    color: #555;
    font-size: .72rem;
    font-weight: 400;
}

.referral-report-confidential i {
    display: none;
}

.referral-report-summary {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    column-gap: 34px;
    row-gap: 4px;
    margin: 15px 0 0;
    padding: 8px 0;
    border-top: 1px solid #777;
    border-bottom: 1px solid #777;
}

.referral-summary-item {
    display: grid;
    grid-template-columns: 145px minmax(0, 1fr);
    gap: 6px;
    padding: 2px 0;
    border: 0;
}

.referral-summary-item span,
.referral-summary-item strong {
    display: block;
    margin: 0;
    font-size: .84rem;
    line-height: 1.5;
}

.referral-summary-item span {
    color: #333;
    font-weight: 600;
}

.referral-summary-item span::after {
    content: ":";
}

.referral-summary-item strong {
    color: #111;
    font-weight: 400;
}

.referral-report-section {
    margin-top: 24px;
}

.referral-section-heading {
    display: block;
    margin-bottom: 8px;
}

.referral-section-no {
    display: none;
}

.referral-section-heading h3 {
    margin: 0;
    padding-bottom: 4px;
    border-bottom: 1px solid #222;
    color: #000;
    font-size: .98rem;
    font-weight: 700;
}

.referral-section-heading p {
    margin: 4px 0 0;
    color: #555;
    font-size: .75rem;
    font-weight: 400;
}

.referral-info-grid,
.referral-current-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    column-gap: 30px;
    margin: 0;
    border: 0;
}

.referral-info-grid > div,
.referral-current-grid > div {
    display: grid;
    grid-template-columns: 110px minmax(0, 1fr);
    gap: 8px;
    padding: 6px 0;
    border: 0;
    border-bottom: 1px dotted #bbb;
}

.referral-info-grid dt,
.referral-current-grid dt,
.referral-info-grid dd,
.referral-current-grid dd {
    margin: 0;
    font-size: .84rem;
    line-height: 1.55;
    overflow-wrap: anywhere;
}

.referral-info-grid dt,
.referral-current-grid dt {
    color: #333;
    font-weight: 600;
}

.referral-info-grid dt::after,
.referral-current-grid dt::after {
    content: ":";
}

.referral-info-grid dd,
.referral-current-grid dd {
    color: #111;
    font-weight: 400;
}

.referral-context-list {
    margin-top: 4px;
    border: 0;
}

.referral-context-row {
    display: grid;
    grid-template-columns: 170px minmax(0, 1fr);
    border: 0;
    border-bottom: 1px dotted #bbb;
}

.referral-context-label,
.referral-context-value {
    padding: 7px 0;
    background: transparent;
    color: #111;
    font-size: .84rem;
    line-height: 1.65;
    overflow-wrap: anywhere;
}

.referral-context-label {
    color: #333;
    font-weight: 600;
}

.referral-context-label::after {
    content: ":";
}

.referral-round-list {
    display: block;
}

.referral-round-card {
    margin: 0;
    padding: 12px 0 14px;
    border: 0;
    border-top: 1px solid #888;
    border-radius: 0;
    overflow: visible;
    background: transparent;
}

.referral-round-card:first-child {
    border-top: 0;
    padding-top: 2px;
}

.referral-round-header {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 14px;
    padding: 0 0 6px;
    border: 0;
    border-bottom: 1px dotted #aaa;
    background: transparent;
}

.referral-round-title {
    display: flex;
    align-items: baseline;
    gap: 10px;
    min-width: 0;
}

.referral-round-title span,
.referral-round-title strong,
.referral-round-date {
    color: #111;
    line-height: 1.45;
}

.referral-round-title span {
    font-size: .82rem;
    font-weight: 700;
    white-space: nowrap;
}

.referral-round-title strong {
    font-size: .9rem;
    font-weight: 600;
}

.referral-round-date {
    font-size: .8rem;
    font-weight: 400;
    white-space: nowrap;
}

.referral-round-meta {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    column-gap: 18px;
    padding: 5px 0;
    border: 0;
    border-bottom: 1px dotted #bbb;
}

.referral-round-meta > div {
    display: flex;
    gap: 6px;
    padding: 0;
    border: 0;
    min-width: 0;
}

.referral-round-meta span,
.referral-round-meta strong {
    display: inline;
    margin: 0;
    font-size: .78rem;
    line-height: 1.45;
}

.referral-round-meta span {
    color: #444;
    font-weight: 600;
}

.referral-round-meta span::after {
    content: ":";
}

.referral-round-meta strong {
    color: #111;
    font-weight: 400;
}

.referral-narrative-grid {
    display: block;
}

.referral-narrative-block {
    display: grid;
    grid-template-columns: 145px minmax(0, 1fr);
    gap: 10px;
    min-width: 0;
    padding: 7px 0;
    border: 0;
    border-bottom: 1px dotted #bbb;
}

.referral-narrative-block + .referral-narrative-block {
    border-left: 0;
}

.referral-narrative-block h4,
.referral-narrative-block div {
    margin: 0;
    font-size: .83rem;
    line-height: 1.65;
    overflow-wrap: anywhere;
}

.referral-narrative-block h4 {
    color: #333;
    font-weight: 600;
}

.referral-narrative-block h4::after {
    content: ":";
}

.referral-narrative-block div {
    color: #111;
    font-weight: 400;
}

.referral-detail-line {
    display: grid;
    grid-template-columns: 145px minmax(0, 1fr);
    gap: 10px;
    padding: 7px 0;
    border: 0;
    border-bottom: 1px dotted #bbb;
    background: transparent;
}

.referral-detail-line span,
.referral-detail-line div {
    margin: 0;
    font-size: .82rem;
    line-height: 1.6;
}

.referral-detail-line span {
    color: #333;
    font-weight: 600;
}

.referral-detail-line span::after {
    content: ":";
}

.referral-detail-line div {
    color: #111;
}

.referral-next-plan,
.referral-current-plan {
    display: block;
    margin: 0;
    padding: 4px 0 0;
    border: 0;
    border-top: 1px solid #777;
    background: transparent;
}

.referral-next-plan > div,
.referral-current-plan > div {
    display: grid;
    grid-template-columns: 180px minmax(0, 1fr);
    gap: 10px;
    padding: 6px 0;
    border: 0;
    border-bottom: 1px dotted #bbb;
}

.referral-next-plan > div + div,
.referral-current-plan > div + div {
    border-left: 0;
}

.referral-next-plan span,
.referral-next-plan strong,
.referral-current-plan span,
.referral-current-plan strong {
    display: block;
    margin: 0;
    font-size: .82rem;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

.referral-next-plan span,
.referral-current-plan span {
    color: #333;
    font-weight: 600;
}

.referral-next-plan span::after,
.referral-current-plan span::after {
    content: ":";
}

.referral-next-plan strong,
.referral-current-plan strong {
    color: #111;
    font-weight: 400;
}

.referral-goal-complete {
    display: block;
    padding: 7px 0 0;
    border: 0;
    color: #111;
    font-size: .82rem;
    font-weight: 600;
    background: transparent;
}

.referral-goal-complete i {
    display: none;
}

.referral-empty-state {
    padding: 12px 0;
    border: 0;
    border-top: 1px solid #999;
    border-bottom: 1px solid #999;
    border-radius: 0;
    text-align: left;
    background: transparent;
}

.referral-empty-state strong,
.referral-empty-state span {
    display: block;
    color: #111;
    font-size: .83rem;
    line-height: 1.6;
}

.referral-empty-state strong {
    margin-bottom: 2px;
    font-weight: 600;
}

.referral-empty-state span {
    color: #555;
    font-weight: 400;
}

.referral-current-plan {
    margin-top: 4px;
}

.referral-current-complete {
    padding: 8px 0;
    border: 0;
    border-top: 1px solid #999;
    border-bottom: 1px solid #999;
    background: transparent;
}

.referral-current-complete strong,
.referral-current-complete span {
    display: block;
    color: #111;
    font-size: .83rem;
    line-height: 1.6;
}

.referral-current-complete strong {
    font-weight: 600;
}

.referral-current-complete span {
    margin-top: 2px;
    color: #555;
    font-weight: 400;
}

.referral-current-pending {
    border-style: solid;
}

.referral-report-footer {
    display: flex;
    justify-content: space-between;
    gap: 14px;
    margin-top: 26px;
    padding-top: 7px;
    border-top: 1px solid #777;
    color: #555;
    font-size: .7rem;
}

@media (max-width: 767.98px) {
    .referral-report-page {
        padding: 10px 6px 24px;
    }

    .referral-report-paper {
        padding: 24px 16px;
    }

    .referral-report-summary,
    .referral-info-grid,
    .referral-current-grid,
    .referral-round-meta {
        grid-template-columns: 1fr;
    }

    .referral-summary-item,
    .referral-info-grid > div,
    .referral-current-grid > div,
    .referral-context-row,
    .referral-narrative-block,
    .referral-detail-line,
    .referral-next-plan > div,
    .referral-current-plan > div {
        grid-template-columns: 1fr;
        gap: 2px;
    }

    .referral-round-meta > div {
        padding: 3px 0;
    }

    .referral-round-header,
    .referral-round-title,
    .referral-report-footer {
        flex-direction: column;
        align-items: flex-start;
    }
}

@page {
    size: A4 portrait;
    margin: 12mm 14mm;
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
        box-shadow: none !important;
    }

    .referral-report-header {
        padding-bottom: 6px !important;
        border-bottom: 1.2pt solid #000 !important;
    }

    .referral-report-eyebrow {
        font-size: 8.5pt !important;
    }

    .referral-report-header h2 {
        font-size: 15pt !important;
    }

    .referral-report-client {
        margin-top: 2px !important;
        font-size: 10pt !important;
    }

    .referral-report-confidential {
        margin-top: 4px !important;
        color: #000 !important;
        font-size: 7.5pt !important;
    }

    .referral-report-summary {
        margin-top: 7px !important;
        padding: 5px 0 !important;
        column-gap: 22px !important;
        border-color: #000 !important;
    }

    .referral-summary-item {
        grid-template-columns: 120px minmax(0, 1fr) !important;
        gap: 4px !important;
        padding: 1px 0 !important;
    }

    .referral-summary-item span,
    .referral-summary-item strong,
    .referral-info-grid dt,
    .referral-info-grid dd,
    .referral-current-grid dt,
    .referral-current-grid dd,
    .referral-context-label,
    .referral-context-value,
    .referral-round-title span,
    .referral-round-title strong,
    .referral-round-date,
    .referral-round-meta span,
    .referral-round-meta strong,
    .referral-narrative-block h4,
    .referral-narrative-block div,
    .referral-detail-line span,
    .referral-detail-line div,
    .referral-next-plan span,
    .referral-next-plan strong,
    .referral-current-plan span,
    .referral-current-plan strong,
    .referral-goal-complete,
    .referral-current-complete strong,
    .referral-current-complete span {
        color: #000 !important;
        font-size: 8.5pt !important;
        line-height: 1.4 !important;
    }

    .referral-report-section {
        margin-top: 10px !important;
    }

    .referral-section-heading {
        margin-bottom: 4px !important;
        break-after: avoid;
        page-break-after: avoid;
    }

    .referral-section-heading h3 {
        padding-bottom: 2px !important;
        border-color: #000 !important;
        font-size: 10pt !important;
    }

    .referral-section-heading p {
        margin-top: 2px !important;
        color: #000 !important;
        font-size: 7.5pt !important;
    }

    .referral-info-grid,
    .referral-current-grid {
        column-gap: 18px !important;
    }

    .referral-info-grid > div,
    .referral-current-grid > div,
    .referral-context-row,
    .referral-narrative-block,
    .referral-detail-line,
    .referral-next-plan > div,
    .referral-current-plan > div {
        padding-top: 3px !important;
        padding-bottom: 3px !important;
        border-color: #777 !important;
    }

    .referral-round-card {
        padding: 6px 0 7px !important;
        border-color: #000 !important;
        break-inside: auto;
        page-break-inside: auto;
    }

    .referral-round-header {
        padding-bottom: 3px !important;
        border-color: #777 !important;
        break-after: avoid;
        page-break-after: avoid;
    }

    .referral-round-meta {
        padding: 3px 0 !important;
        column-gap: 10px !important;
        border-color: #777 !important;
    }

    .referral-next-plan,
    .referral-current-plan,
    .referral-current-complete,
    .referral-empty-state {
        border-color: #000 !important;
    }

    .referral-report-footer {
        margin-top: 10px !important;
        padding-top: 4px !important;
        border-color: #000 !important;
        color: #000 !important;
        font-size: 7pt !important;
    }
}
</style>

@endsection