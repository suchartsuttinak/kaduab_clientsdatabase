@php
    use Carbon\Carbon;

    $client = $estimate->client;

    $fullName = $client->full_name ?? $client->fullname ?? '-';
    $birthDate = !empty($client->birth_date) ? Carbon::parse($client->birth_date) : null;
    $age = $birthDate ? $birthDate->age : '-';

    $thaiDate = function ($date) {
        if (!$date) return '-';
        $months = [
            1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
            5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
            9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
        ];
        $d = Carbon::parse($date);
        return $d->day . ' ' . $months[$d->month] . ' ' . ($d->year + 543);
    };
@endphp
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>รายงานการติดตามและประเมินครอบครัวเด็ก</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
*, *::before, *::after{
    box-sizing:border-box;
}

body{
    font-family:"TH Sarabun New","Sarabun",sans-serif;
    font-size:14px;
    line-height:1.55;
    color:#0f172a;
    background:#eef3f8;
    margin:0;
    padding:22px 12px;
}

.estimate-report-page{
    max-width:1020px;
    margin:0 auto;
}

.estimate-report-paper{
    background:#ffffff;
    border:1px solid #dde6ef;
    border-radius:18px;
    box-shadow:0 12px 36px rgba(15,23,42,.06);
    overflow:hidden;
}

.estimate-report-toolbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:10px;
    padding:12px 18px;
    border-bottom:1px solid #e6edf5;
    background:#f8fbff;
    flex-wrap:wrap;
}

.estimate-report-toolbar-group{
    display:flex;
    gap:8px;
}

.estimate-report-btn{
    display:inline-flex;
    align-items:center;
    gap:6px;
    height:36px;
    padding:0 14px;
    border-radius:10px;
    border:1px solid #d1d9e6;
    background:#ffffff;
    color:#0f172a;
    font-size:14px;
    font-weight:600;
    text-decoration:none;
    transition:.2s ease;
}

.estimate-report-btn:hover{
    background:#f1f5f9;
}

.estimate-report-btn-primary{
    background:#2563eb;
    border-color:#2563eb;
    color:#ffffff;
}

.estimate-report-btn-primary:hover{
    background:#1d4ed8;
}

.estimate-report-content{
    padding:28px 32px 32px;
}

.estimate-report-head{
    text-align:center;
    margin-bottom:18px;
    padding-bottom:14px;
    border-bottom:1px solid #e2e8f0;
}

.estimate-report-head-title{
    font-size:16px;
    font-weight:800;
    margin:4px 0;
}

.estimate-report-meta{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:8px 16px;
    margin-bottom:16px;
    padding-bottom:12px;
    border-bottom:1px solid #e2e8f0;
}

.estimate-report-meta-label{
    font-weight:700;
    color:#475569;
}

.estimate-report-meta-value{
    color:#0f172a;
}

.estimate-report-section{
    margin-bottom:16px;
}

.estimate-report-section-title{
    font-size:16px;
    font-weight:800;
    margin-bottom:8px;
    padding-bottom:4px;
    border-bottom:1px solid #e5edf5;
}

.estimate-report-lines{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:6px 20px;
}

.estimate-report-line{
    display:flex;
    gap:8px;
}

.estimate-report-line.full{
    grid-column:1 / -1;
}

.estimate-report-label{
    min-width:160px;
    font-weight:700;
    color:#475569;
}

.estimate-report-value{
    flex:1;
}

.estimate-report-inline-badge{
    padding:2px 10px;
    border-radius:999px;
    background:#eef4ff;
    color:#1d4ed8;
    font-size:13px;
    font-weight:700;
}

.estimate-report-empty{
    color:#64748b;
    font-size:14px;
    padding:6px 0;
}

.text-block{
    white-space:pre-wrap;
    overflow-wrap:anywhere;
    word-break:break-word;
}

.estimate-report-section,
.estimate-report-meta,
.estimate-report-picture-item{
    break-inside:avoid;
    page-break-inside:avoid;
}

/* ===== IMAGE: แก้เฉพาะส่วนนี้ ไม่ให้รูปจอใหญ่เต็มแถว ===== */
.estimate-report-pictures{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(220px, 280px));
    gap:12px;
    justify-content:start;
    align-items:start;
}

.estimate-report-picture-item{
    width:100%;
    max-width:280px;
}

.estimate-report-picture-frame{
    width:100%;
}

.estimate-report-picture-frame img{
    width:100%;
    height:180px;
    object-fit:contain;
    object-position:center;
    background:#f8fafc;
    border-radius:10px;
    border:1px solid #dbe3ec;
    display:block;
}

.estimate-report-picture-caption{
    font-size:13px;
    color:#334155;
    margin-top:6px;
}

/* กรณีมีรูปเดียว ไม่ให้ยืดเต็มจอ */
.estimate-report-pictures:has(.estimate-report-picture-item:only-child){
    grid-template-columns:280px;
}

/* ===== MOBILE ===== */
@media (max-width: 768px){
    .estimate-report-content{
        padding:18px 14px;
    }

    .estimate-report-meta{
        grid-template-columns:1fr;
    }

    .estimate-report-lines{
        grid-template-columns:1fr;
    }

    .estimate-report-line{
        flex-direction:column;
    }

    .estimate-report-label{
        min-width:auto;
    }

    .estimate-report-pictures{
        grid-template-columns:1fr;
    }

    .estimate-report-picture-item{
        max-width:100%;
    }

    .estimate-report-picture-frame img{
        height:220px;
    }
}

/* ===== PRINT ===== */
@page{
    size:A4 portrait;
    margin:12mm;
}

@media print{
    html,
    body{
        width:auto;
        min-height:auto;
    }

    body{
        background:#fff;
        padding:0;
    }

    .estimate-report-toolbar{
        display:none;
    }

    .estimate-report-paper{
        border:none;
        box-shadow:none;
    }

    .estimate-report-content{
        padding:0;
    }

    .estimate-report-head{
        margin-top:0;
    }

    .estimate-report-section-title{
        break-after:avoid;
        page-break-after:avoid;
    }

    .estimate-report-pictures{
        grid-template-columns:repeat(2, 1fr);
        gap:10px;
    }

    .estimate-report-picture-item{
        max-width:100%;
    }

    .estimate-report-picture-frame img{
        height:180px;
        object-fit:contain;
        border-radius:6px;
    }
}
    </style>
</head>
<body>
    <div class="estimate-report-page">
        <div class="estimate-report-paper">

            <div class="estimate-report-toolbar">
                <div class="estimate-report-toolbar-group">
                    <a href="{{ route('estimate.show', $estimate->client_id) }}" class="estimate-report-btn">
                        ← กลับหน้ารายการ
                    </a>
                </div>

                <div class="estimate-report-toolbar-group">
                    <button type="button" class="estimate-report-btn estimate-report-btn-primary" onclick="window.print()">
                        พิมพ์รายงาน
                    </button>
                </div>
            </div>

            <div class="estimate-report-content">

                <div class="estimate-report-head">
                    <h3 class="estimate-report-head-title">รายงานการติดตามและประเมินครอบครัวเด็ก</h3>
                </div>

                <div class="estimate-report-meta">
                    <div class="estimate-report-meta-item">
                        <span class="estimate-report-meta-label">ชื่อ-สกุล:</span>
                        <span class="estimate-report-meta-value">{{ $fullName }}</span>
                    </div>
                    <div class="estimate-report-meta-item">
                        <span class="estimate-report-meta-label">อายุ:</span>
                        <span class="estimate-report-meta-value">{{ $age }} ปี</span>
                    </div>
                    <div class="estimate-report-meta-item">
                        <span class="estimate-report-meta-label">ครั้งที่ติดตาม:</span>
                        <span class="estimate-report-meta-value">{{ $estimate->count ?? '-' }}</span>
                    </div>
                </div>

                <div class="estimate-report-section">
                    <h2 class="estimate-report-section-title">ข้อมูลพื้นฐาน</h2>

                    <div class="estimate-report-lines">
                        <div class="estimate-report-line">
                            <div class="estimate-report-label">วันที่ติดตาม</div>
                            <div class="estimate-report-value">{{ $thaiDate($estimate->date) }}</div>
                        </div>

                        <div class="estimate-report-line">
                            <div class="estimate-report-label">การดำเนินงาน</div>
                            <div class="estimate-report-value">{{ $estimate->follo_no ?: '-' }}</div>
                        </div>

                        <div class="estimate-report-line full">
                            <div class="estimate-report-label">ผลการติดตาม</div>
                            <div class="estimate-report-value text-block">{{ $estimate->results ?: '-' }}</div>
                        </div>

                        <div class="estimate-report-line">
                            <div class="estimate-report-label">ผู้ติดตาม / ผู้ประเมิน</div>
                            <div class="estimate-report-value">{{ $estimate->teacher ?: '-' }}</div>
                        </div>

                        <div class="estimate-report-line">
                            <div class="estimate-report-label">หมายเหตุ</div>
                            <div class="estimate-report-value">{{ $estimate->remark ?: '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="estimate-report-section">
                    <h2 class="estimate-report-section-title">ข้อมูลครอบครัวเพิ่มเติม</h2>

                    <div class="estimate-report-lines">
                        <div class="estimate-report-line">
                            <div class="estimate-report-label">รายได้ครอบครัวเฉลี่ย/เดือน</div>
                            <div class="estimate-report-value">
                                {{ $estimate->family_income !== null ? number_format($estimate->family_income, 2) . ' บาท' : '-' }}
                            </div>
                        </div>

                        <div class="estimate-report-line">
                            <div class="estimate-report-label">อาชีพผู้ปกครอง</div>
                            <div class="estimate-report-value">{{ $estimate->guardian_job ?: '-' }}</div>
                        </div>

                        <div class="estimate-report-line">
                            <div class="estimate-report-label">ความเพียงพอของรายได้</div>
                            <div class="estimate-report-value">
                                @if($estimate->income_sufficiency)
                                    <span class="estimate-report-inline-badge">{{ $estimate->income_sufficiency }}</span>
                                @else
                                    -
                                @endif
                            </div>
                        </div>

                        <div class="estimate-report-line">
                            <div class="estimate-report-label">สภาพที่อยู่อาศัย</div>
                            <div class="estimate-report-value">
                                @if($estimate->housing_condition)
                                    <span class="estimate-report-inline-badge">{{ $estimate->housing_condition }}</span>
                                @else
                                    -
                                @endif
                            </div>
                        </div>

                      @if(
                            $estimate->income_sufficiency === 'ไม่เพียงพอ'
                            && !empty($estimate->income_reason)
                        )
                            <div class="estimate-report-line full">
                                <div class="estimate-report-label">เนื่องจาก</div>
                                <div class="estimate-report-value text-block">
                                    {{ $estimate->income_reason }}
                                </div>
                            </div>
                        @endif

                        <div class="estimate-report-line full">
                            <div class="estimate-report-label">หนี้สิน (ถ้ามี)</div>
                            <div class="estimate-report-value text-block">{{ $estimate->debt ?: '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="estimate-report-section">
                    <h2 class="estimate-report-section-title">ภาพประกอบ</h2>

                    @if($estimate->pictures->isEmpty())
                        <div class="estimate-report-empty">ไม่มีรูปภาพประกอบ</div>
                    @else
                      <div class="estimate-report-pictures">
                            @foreach($estimate->pictures as $index => $picture)

                                <div class="estimate-report-picture-item">
                                    <div class="estimate-report-picture-frame">

                                        <img src="{{ route('estimate.image.view', $picture->id) }}"
                                            alt="รูปภาพประกอบ {{ $index + 1 }}"
                                            decoding="async">

                                        <div class="estimate-report-picture-caption">
                                            รูปภาพประกอบ {{ $index + 1 }}
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</body>
</html>