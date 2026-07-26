@extends('admin_client.admin_client')

@section('content')

<style>

.snap-report-page{
    padding:24px 0;
}

.snap-report-shell{
    max-width:1200px;
    margin:auto;
}

.snap-report-card{
    border:none;
    border-radius:22px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
}

.snap-report-header{
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    color:#fff;
    padding:28px;
}

.snap-report-title{
    font-size:1.7rem;
    font-weight:700;
    margin-bottom:6px;
}

.snap-report-subtitle{
    opacity:.92;
}

.snap-report-body{
    background:#fff;
    padding:28px;
}

.snap-info-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:16px;
    margin-bottom:28px;
}

.snap-info-box{
    border:1px solid #e5e7eb;
    border-radius:16px;
    padding:16px;
    background:#f8fafc;
}

.snap-info-label{
    color:#64748b;
    font-size:.92rem;
    margin-bottom:6px;
}

.snap-info-value{
    font-weight:700;
    color:#0f172a;
}

.snap-score-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(240px,1fr));
    gap:20px;
    margin-bottom:30px;
}

.snap-score-card{
    border-radius:20px;
    color:#fff;
    padding:24px;
}

.snap-score-card h4{
    font-size:1rem;
    margin-bottom:12px;
}

.snap-score-value{
    font-size:2.5rem;
    font-weight:700;
    line-height:1;
}

.snap-score-level{
    margin-top:14px;
    font-size:.95rem;
}

.snap-blue{
    background:#2563eb;
}

.snap-orange{
    background:#d97706;
}

.snap-red{
    background:#dc2626;
}

.snap-section{
    border:1px solid #e5e7eb;
    border-radius:18px;
    overflow:hidden;
    margin-bottom:24px;
}

.snap-section-header{
    background:#f8fafc;
    padding:16px 20px;
    font-weight:700;
    border-bottom:1px solid #e5e7eb;
}

.snap-section-body{
    padding:20px;
    line-height:1.8;
    white-space:pre-line;
}

.snap-toolbar{
    display:flex;
    justify-content:space-between;
    gap:12px;
    flex-wrap:wrap;
    margin-bottom:20px;
}

.snap-table-wrap{
    overflow:auto;
}

@media print{

    .snap-toolbar,
    .main-header,
    .left-side-bar,
    .navbar-custom,
    .footer{
        display:none !important;
    }

    .container-fluid{
        padding:0 !important;
        margin:0 !important;
    }

    .snap-report-card{
        box-shadow:none !important;
        border:none !important;
    }

}

</style>

<div class="container-fluid snap-report-page">

    <div class="snap-report-shell">

        <div class="snap-toolbar">

            <a href="{{ route('snap-iv.index', $client->id) }}"
               class="btn btn-light border">

                <i class="bi bi-arrow-left"></i>
                กลับ

            </a>

           <div class="d-flex gap-2 flex-wrap">

                    <a href="{{ route('snap-iv.official-report', $screening->id) }}"
                    class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-text"></i>
                        รายงานทางการ
                    </a>

                    <a href="{{ route('snap-iv.official-report', $screening->id) }}"
                    class="btn btn-primary">
                        <i class="bi bi-printer"></i>
                        พิมพ์รายงาน
                    </a>

                </div>

        </div>

        <div class="snap-report-card">

            <div class="snap-report-header">

                <div class="snap-report-title">
                    รายงานแบบประเมิน SNAP-IV
                </div>

                <div class="snap-report-subtitle">
                    แบบประเมินคัดกรองพฤติกรรมเด็กและวัยรุ่น
                </div>

            </div>

            <div class="snap-report-body">

                <div class="snap-info-grid">

                    <div class="snap-info-box">
                        <div class="snap-info-label">
                            ชื่อผู้รับบริการ
                        </div>

                        <div class="snap-info-value">
                            {{ $client->first_name }} {{ $client->last_name }}
                        </div>
                    </div>

                    <div class="snap-info-box">
                        <div class="snap-info-label">
                            วันที่ประเมิน
                        </div>

                        <div class="snap-info-value">
                            {{ $screening->screening_date?->format('d/m/Y') }}
                        </div>
                    </div>

                    <div class="snap-info-box">
                        <div class="snap-info-label">
                            ผู้ประเมิน
                        </div>

                        <div class="snap-info-value">
                            {{ $screening->observer_name ?: '-' }}
                        </div>
                    </div>

                    <div class="snap-info-box">
                        <div class="snap-info-label">
                            ความสัมพันธ์
                        </div>

                        <div class="snap-info-value">
                            {{ $screening->relationship ?: '-' }}
                        </div>
                    </div>

                    <div class="snap-info-box">
                        <div class="snap-info-label">
                            อายุ
                        </div>

                        <div class="snap-info-value">
                            {{ $screening->age_text ?: '-' }}
                        </div>
                    </div>

                    <div class="snap-info-box">
                        <div class="snap-info-label">
                            ชั้นเรียน
                        </div>

                        <div class="snap-info-value">
                            {{ $screening->class_level ?: '-' }}
                        </div>
                    </div>

                </div>

                <div class="snap-score-grid">

                    <div class="snap-score-card snap-blue">

                        <h4>
                            อาการขาดสมาธิ
                        </h4>

                        <div class="snap-score-value">
                            {{ $screening->inattention_score }}
                        </div>

                        <div class="snap-score-level">
                            {{ $screening->inattention_level }}
                        </div>

                    </div>

                    <div class="snap-score-card snap-orange">

                        <h4>
                            ซน / หุนหันพลันแล่น
                        </h4>

                        <div class="snap-score-value">
                            {{ $screening->hyperactivity_score }}
                        </div>

                        <div class="snap-score-level">
                            {{ $screening->hyperactivity_level }}
                        </div>

                    </div>

                    <div class="snap-score-card snap-red">

                        <h4>
                            ดื้อ / ต่อต้าน
                        </h4>

                        <div class="snap-score-value">
                            {{ $screening->oppositional_score }}
                        </div>

                        <div class="snap-score-level">
                            {{ $screening->oppositional_level }}
                        </div>

                    </div>

                </div>

                <div class="snap-section">

                    <div class="snap-section-header">
                        สรุปผลการประเมิน
                    </div>

                    <div class="snap-section-body">
                        {{ $screening->summary }}
                    </div>

                </div>

                <div class="snap-section">

                    <div class="snap-section-header">
                        คำแนะนำ
                    </div>

                    <div class="snap-section-body">
                        {{ $screening->recommendation }}
                    </div>

                </div>

                @if($screening->remark)

                    <div class="snap-section">

                        <div class="snap-section-header">
                            หมายเหตุเพิ่มเติม
                        </div>

                        <div class="snap-section-body">
                            {{ $screening->remark }}
                        </div>

                    </div>

                @endif

                <div class="snap-section">

                    <div class="snap-section-header">
                        รายละเอียดรายข้อ
                    </div>

                    <div class="snap-table-wrap">

                        <table class="table align-middle mb-0">

                            <thead class="table-light">

                                <tr>
                                    <th width="80">
                                        ข้อ
                                    </th>

                                    <th>
                                        รายการประเมิน
                                    </th>

                                    <th width="120">
                                        คะแนน
                                    </th>
                                </tr>

                            </thead>

                            <tbody>

                                @foreach($screening->items as $item)

                                    <tr>

                                        <td>
                                            {{ $item->item_no }}
                                        </td>

                                        <td>
                                            {{ $item->question }}
                                        </td>

                                        <td class="fw-bold">
                                            {{ $item->score }}
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection