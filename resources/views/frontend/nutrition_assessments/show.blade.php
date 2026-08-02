@extends('admin_client.admin_client')

@section('content')
    <div class="container-fluid py-4 nutrition-show-page">

        <div class="nutrition-card">

            <div class="nutrition-header">
                <div>
                    <h4 class="mb-1">ผลการประเมินภาวะโภชนาการ</h4>
                    <div class="text-muted small">
                        {{ $client->prefix ?? '' }}{{ $client->first_name ?? '' }}
                        {{ $client->last_name ?? '' }}
                    </div>
                </div>

                <div class="nutrition-header-actions">
                    <a href="{{ route('nutrition_assessments.index', $client->id) }}"
                        class="nutrition-btn nutrition-btn-secondary">
                        <i class="bi bi-arrow-left"></i>
                        <span>กลับหน้ารายการ</span>
                    </a>
                </div>
            </div>

            <hr>

            @php
                $firstGrowth = isset($growthRecords) ? $growthRecords->first() : null;
                $latestGrowth = isset($growthRecords) ? $growthRecords->last() : null;
                $growthCount = isset($growthRecords) ? $growthRecords->count() : 0;

                $heightChange = null;
                $weightChange = null;

                if ($firstGrowth && $latestGrowth && $growthCount > 1) {
                    $heightChange = ((float) $latestGrowth->height_cm) - ((float) $firstGrowth->height_cm);
                    $weightChange = ((float) $latestGrowth->weight_kg) - ((float) $firstGrowth->weight_kg);
                }
            @endphp

            <div class="assessment-mini-grid">

                <div class="assessment-mini-item">
                    <div class="mini-icon mini-blue">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                    <div>
                        <span>วันที่ชั่งวัด</span>
                        <strong>
                            {{ $assessment->assessment_date
                                ? $assessment->assessment_date->format('d/m/') . ($assessment->assessment_date->year + 543)
                                : '-' }}
                        </strong>
                    </div>
                </div>

                <div class="assessment-mini-item">
                    <div class="mini-icon mini-green">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <span>อายุ</span>
                        <strong>
                            {{ $assessment->age_year ?? '-' }} ปี
                            {{ $assessment->age_month ?? '-' }} เดือน
                        </strong>
                    </div>
                </div>

                <div class="assessment-mini-item">
                    <div class="mini-icon mini-purple">
                        <i class="bi bi-gender-ambiguous"></i>
                    </div>
                    <div>
                        <span>เพศ</span>
                        <strong>
                            {{ $assessment->gender === 'male' ? 'ชาย' : ($assessment->gender === 'female' ? 'หญิง' : '-') }}
                        </strong>
                    </div>
                </div>

                <div class="assessment-mini-item">
                    <div class="mini-icon mini-cyan">
                        <i class="bi bi-rulers"></i>
                    </div>
                    <div>
                        <span>ส่วนสูง</span>
                        <strong>
                            {{ $assessment->height_cm ? number_format($assessment->height_cm, 2) : '-' }} ซม.
                        </strong>
                    </div>
                </div>

                <div class="assessment-mini-item">
                    <div class="mini-icon mini-orange">
                        <i class="bi bi-speedometer2"></i>
                    </div>
                    <div>
                        <span>น้ำหนัก</span>
                        <strong>
                            {{ $assessment->weight_kg ? number_format($assessment->weight_kg, 2) : '-' }} กก.
                        </strong>
                    </div>
                </div>

                <div class="assessment-mini-item">
                    <div class="mini-icon mini-red">
                        <i class="bi bi-activity"></i>
                    </div>
                    <div>
                        <span>BMI</span>
                        <strong>
                            {{ $assessment->bmi ? number_format($assessment->bmi, 2) : '-' }}
                        </strong>
                    </div>
                </div>

            </div>

            <div class="growth-summary-grid mt-4">

                <div class="growth-summary-box">
                    <span>ประเมินทั้งหมด</span>
                    <strong>{{ $growthCount }}</strong>
                    <small>จำนวนครั้งที่ประเมิน</small>
                </div>

                <div class="growth-summary-box">
                    <span>BMI ล่าสุด</span>
                    <strong>
                        {{ $latestGrowth && $latestGrowth->bmi ? number_format($latestGrowth->bmi, 2) : '-' }}
                    </strong>
                    <small>ค่าดัชนีมวลกายล่าสุด</small>
                </div>

                <div class="growth-summary-box">
                    <span>ส่วนสูงล่าสุด</span>
                    <strong>
                        {{ $latestGrowth && $latestGrowth->height_cm ? number_format($latestGrowth->height_cm, 2) : '-' }}
                        <em>ซม.</em>
                    </strong>
                    <small>จากการประเมินล่าสุด</small>
                </div>

                <div class="growth-summary-box">
                    <span>น้ำหนักล่าสุด</span>
                    <strong>
                        {{ $latestGrowth && $latestGrowth->weight_kg ? number_format($latestGrowth->weight_kg, 2) : '-' }}
                        <em>กก.</em>
                    </strong>
                    <small>จากการประเมินล่าสุด</small>
                </div>

                <div class="growth-summary-box">
                    <span>ส่วนสูงเปลี่ยนแปลง</span>
                    <strong>
                        {{ $heightChange !== null ? ($heightChange >= 0 ? '+' : '') . number_format($heightChange, 2) : '-' }}
                        <em>ซม.</em>
                    </strong>
                    <small>เทียบครั้งแรกกับครั้งล่าสุด</small>
                </div>

                <div class="growth-summary-box">
                    <span>น้ำหนักเปลี่ยนแปลง</span>
                    <strong>
                        {{ $weightChange !== null ? ($weightChange >= 0 ? '+' : '') . number_format($weightChange, 2) : '-' }}
                        <em>กก.</em>
                    </strong>
                    <small>เทียบครั้งแรกกับครั้งล่าสุด</small>
                </div>

            </div>

            {{-- กราฟการเจริญเติบโตย้อนหลัง --}}
            <div class="growth-card mt-4">
                <div class="growth-header">
                    <div>
                        <h5>
                            <i class="bi bi-graph-up-arrow me-2"></i>
                            กราฟการเจริญเติบโตย้อนหลัง
                        </h5>
                        <small>
                            แสดงแนวโน้มส่วนสูงและน้ำหนักจากผลประเมินที่ผ่านมา
                        </small>
                    </div>

                    <div class="growth-badge">
                        {{ $growthCount }} ครั้ง
                    </div>
                </div>

                @if ($growthCount > 0)
                    <div class="growth-chart-wrapper">
                        <canvas id="growthChart"></canvas>
                    </div>
                @else
                    <div class="growth-empty">
                        <i class="bi bi-bar-chart-line"></i>
                        <div>ยังไม่มีข้อมูลย้อนหลังสำหรับแสดงกราฟ</div>
                    </div>
                @endif
            </div>

            <div class="summary-box mt-4">
                <div class="summary-title">
                    สรุปผลประเมินเบื้องต้น
                </div>

                <div class="summary-status">
                    {{ $assessment->nutrition_status ?: 'ยังไม่มีผลประเมิน' }}
                </div>

                <p class="summary-note">
                    หมายเหตุ: ผลนี้เป็นการประเมินเบื้องต้นจาก BMI ในระบบ ส่วนเกณฑ์กราฟกรมอนามัยที่อัปโหลดเป็นกราฟ Percentile
                    5–19 ปี สำหรับดูประกอบการติดตามการเจริญเติบโต
                </p>
            </div>

            @if ($assessment->note)
                <div class="note-box mt-4">
                    <strong>หมายเหตุ</strong>
                    <p>{{ $assessment->note }}</p>
                </div>
            @endif

        </div>

    </div>

    <style>
        .nutrition-show-page {
            background: #f6f8fb;
        }

        .nutrition-card {
            background: #fff;
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 8px 25px rgba(15, 23, 42, .06);
        }

        .nutrition-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .nutrition-header h4 {
            font-weight: 700;
            color: #1e293b;
        }

        .nutrition-header-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .nutrition-btn {
            min-height: 42px;
            border-radius: 12px;
            padding: 10px 18px;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
            transition: transform .18s ease, box-shadow .18s ease, background .18s ease, border-color .18s ease, color .18s ease;
        }

        .nutrition-btn-secondary {
            color: #475569;
            background: #fff;
            border-color: #cbd5e1;
            box-shadow: 0 3px 8px rgba(15, 23, 42, .05);
        }

        .nutrition-btn-secondary:hover,
        .nutrition-btn-secondary:focus {
            color: #1e293b;
            background: #f8fafc;
            border-color: #94a3b8;
            transform: translateY(-1px);
        }

        /* ข้อมูลผลประเมินแบบแถวย่อ */
        .assessment-mini-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }

        .assessment-mini-item {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 185px;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(15, 23, 42, .035);
        }

        .mini-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 34px;
            font-size: 1rem;
        }

        .mini-blue {
            background: #eff6ff;
            color: #2563eb;
        }

        .mini-green {
            background: #ecfdf5;
            color: #059669;
        }

        .mini-purple {
            background: #f5f3ff;
            color: #7c3aed;
        }

        .mini-cyan {
            background: #ecfeff;
            color: #0891b2;
        }

        .mini-orange {
            background: #fff7ed;
            color: #ea580c;
        }

        .mini-red {
            background: #fef2f2;
            color: #dc2626;
        }

        .assessment-mini-item span {
            display: block;
            font-size: .78rem;
            color: #64748b;
            font-weight: 700;
            line-height: 1.1;
        }

        .assessment-mini-item strong {
            display: block;
            margin-top: 3px;
            font-size: .95rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.25;
        }

        /* Dashboard สรุปการเจริญเติบโต */
        .growth-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .growth-summary-box {
            border: 1px solid #e2e8f0;
            border-left: 4px solid #3b82f6;
            border-radius: 18px;
            padding: 20px;
            background: linear-gradient(135deg, #ffffff, #f8fafc);
        }

        .growth-summary-box:nth-child(2) {
            border-left-color: #10b981;
        }

        .growth-summary-box:nth-child(3) {
            border-left-color: #f59e0b;
        }

        .growth-summary-box:nth-child(4) {
            border-left-color: #8b5cf6;
        }

        .growth-summary-box:nth-child(5) {
            border-left-color: #06b6d4;
        }

        .growth-summary-box:nth-child(6) {
            border-left-color: #ec4899;
        }

        .growth-summary-box span {
            display: block;
            color: #64748b;
            font-size: .85rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .growth-summary-box strong {
            display: block;
            font-size: 1.45rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }

        .growth-summary-box em {
            font-style: normal;
            font-size: .9rem;
            color: #64748b;
            margin-left: 4px;
        }

        .growth-summary-box small {
            display: block;
            margin-top: 8px;
            color: #94a3b8;
            font-size: .78rem;
        }

        /* กราฟ */
        .growth-card {
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 22px;
            background: #ffffff;
        }

        .growth-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 18px;
        }

        .growth-header h5 {
            margin: 0 0 4px;
            font-size: 1.05rem;
            font-weight: 800;
            color: #1e293b;
        }

        .growth-header small {
            color: #64748b;
            font-size: .86rem;
        }

        .growth-badge {
            white-space: nowrap;
            border-radius: 999px;
            padding: 6px 12px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: .82rem;
            font-weight: 700;
        }

        .growth-chart-wrapper {
            position: relative;
            height: 360px;
        }

        .growth-empty {
            border: 1px dashed #cbd5e1;
            border-radius: 16px;
            padding: 32px 18px;
            text-align: center;
            color: #64748b;
            background: #f8fafc;
        }

        .growth-empty i {
            display: block;
            font-size: 2rem;
            margin-bottom: 8px;
            color: #94a3b8;
        }

        /* สรุปผล */
        .summary-box {
            border-radius: 18px;
            padding: 22px;
            background: linear-gradient(135deg, #ecfdf5, #f0f9ff);
            border: 1px solid #d1fae5;
            text-align: center;
        }

        .summary-title {
            color: #475569;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .summary-status {
            font-size: 1.6rem;
            font-weight: 800;
            color: #047857;
        }

        .summary-note {
            margin: 12px auto 0;
            max-width: 720px;
            color: #64748b;
            font-size: .88rem;
        }

        .note-box {
            border: 1px dashed #cbd5e1;
            border-radius: 16px;
            padding: 18px;
            background: #fff;
        }

        .note-box p {
            margin: 8px 0 0;
            color: #334155;
        }

        /* Responsive */
        @media(max-width:1200px) {
            .growth-summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width:768px) {
            .assessment-mini-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
            }

            .assessment-mini-item {
                min-width: 0;
            }
        }

        @media(max-width:576px) {
            .nutrition-card {
                padding: 18px;
            }

            .nutrition-header,
            .growth-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .assessment-mini-grid,
            .growth-summary-grid {
                grid-template-columns: 1fr;
            }

            .nutrition-header-actions,
            .nutrition-header-actions .nutrition-btn {
                width: 100%;
            }

            .assessment-mini-item {
                width: 100%;
            }

            .growth-chart-wrapper {
                height: 300px;
            }
        }
    </style>

    @if (isset($growthRecords) && $growthRecords->count() > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const chartElement = document.getElementById('growthChart');

                if (!chartElement) {
                    return;
                }

                const labels = @json(
                    $growthRecords->map(function ($item) {
                            return $item->assessment_date
                                ? $item->assessment_date->format('d/m/') . ($item->assessment_date->year + 543)
                                : '-';
                        })->values());

                const heights = @json(
                    $growthRecords->map(function ($item) {
                            return $item->height_cm !== null ? (float) $item->height_cm : null;
                        })->values());

                const weights = @json(
                    $growthRecords->map(function ($item) {
                            return $item->weight_kg !== null ? (float) $item->weight_kg : null;
                        })->values());

                new Chart(chartElement, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'ส่วนสูง (ซม.)',
                                data: heights,
                                borderWidth: 3,
                                tension: 0.35,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                yAxisID: 'yHeight'
                            },
                            {
                                label: 'น้ำหนัก (กก.)',
                                data: weights,
                                borderWidth: 3,
                                tension: 0.35,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                yAxisID: 'yWeight'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    boxWidth: 8,
                                    boxHeight: 8
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let unit = context.dataset.yAxisID === 'yHeight' ? ' ซม.' : ' กก.';
                                        return context.dataset.label + ': ' + context.formattedValue + unit;
                                    }
                                }
                            }
                        },
                        scales: {
                            yHeight: {
                                type: 'linear',
                                position: 'left',
                                beginAtZero: false,
                                title: {
                                    display: true,
                                    text: 'ส่วนสูง (ซม.)'
                                }
                            },
                            yWeight: {
                                type: 'linear',
                                position: 'right',
                                beginAtZero: false,
                                grid: {
                                    drawOnChartArea: false
                                },
                                title: {
                                    display: true,
                                    text: 'น้ำหนัก (กก.)'
                                }
                            },
                            x: {
                                ticks: {
                                    maxRotation: 0,
                                    autoSkip: true
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endif
@endsection
