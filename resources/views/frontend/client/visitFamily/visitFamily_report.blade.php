@php
    use Carbon\Carbon;

    $fullName = trim((string) ($client->full_name ?? ''));
    if ($fullName === '') {
        $fullName = trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));
    }
    $fullName = $fullName !== '' ? $fullName : '-';

    $birthDate = !empty($client->birth_date) ? Carbon::parse($client->birth_date) : null;
    $age = $birthDate ? $birthDate->age : null;

    $thaiDate = function ($date) {
        if (!$date) {
            return '-';
        }

        $months = [
            1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
            5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
            9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม',
        ];

        $d = Carbon::parse($date);

        return $d->day . ' ' . $months[$d->month] . ' ' . ($d->year + 543);
    };

    $imageCount = $visitFamily->images?->count() ?? 0;
@endphp

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>รายงานการเยี่ยมบ้าน</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 13mm 14mm;
        }

        * {
            box-sizing: border-box;
        }

        html {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body,
        button {
            font-family: "Sarabun", "Noto Sans Thai", "Segoe UI", Tahoma, Arial, sans-serif;
        }

        body {
            margin: 0;
            padding: 22px 12px;
            color: #0f172a;
            background: #eef3f8;
            font-size: 14px;
            line-height: 1.55;
        }

        .visit-report-page {
            width: 100%;
            max-width: 1020px;
            margin: 0 auto;
        }

        .visit-report-paper {
            overflow: hidden;
            background: #fff;
            border: 1px solid #dbe4ef;
            border-radius: 18px;
            box-shadow: 0 12px 36px rgba(15, 23, 42, .07);
        }

        .visit-report-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 18px;
            background: #f8fbff;
            border-bottom: 1px solid #e2e8f0;
            flex-wrap: wrap;
        }

        .visit-report-toolbar-actions {
            display: flex;
            align-items: center;
            gap: 9px;
            flex-wrap: wrap;
        }

        .visit-report-btn {
            min-height: 42px;
            padding: 9px 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            background: #fff;
            color: #334155;
            font-size: .94rem;
            font-weight: 600;
            line-height: 1.2;
            text-decoration: none;
            cursor: pointer;
            transition: transform .18s ease, box-shadow .18s ease,
                        border-color .18s ease, background-color .18s ease;
        }

        .visit-report-btn:hover {
            color: #0f172a;
            background: #f1f5f9;
            border-color: #94a3b8;
            box-shadow: 0 6px 14px rgba(15, 23, 42, .08);
            transform: translateY(-1px);
        }

        .visit-report-btn:active {
            transform: translateY(0);
        }

        .visit-report-btn:focus-visible {
            outline: 0;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .16);
        }

        .visit-report-btn:disabled {
            cursor: wait;
            opacity: 1;
            transform: none;
        }

        .visit-report-btn-primary {
            color: #fff;
            border-color: #1d4ed8;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            box-shadow: 0 7px 16px rgba(37, 99, 235, .18);
        }

        .visit-report-btn-primary:hover,
        .visit-report-btn-primary:focus {
            color: #fff;
            border-color: #1e40af;
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            box-shadow: 0 9px 20px rgba(37, 99, 235, .24);
        }

        .visit-report-print-note {
            color: #64748b;
            font-size: .84rem;
            line-height: 1.45;
        }

        .visit-report-content {
            padding: 28px 32px 34px;
        }

        .visit-report-head {
            margin-bottom: 16px;
            padding-bottom: 14px;
            text-align: center;
            border-bottom: 2px solid #dbeafe;
        }

        .visit-report-head-title {
            margin: 0;
            color: #0f172a;
            font-size: 1.22rem;
            font-weight: 700;
            line-height: 1.45;
        }

        .visit-report-head-subtitle {
            margin-top: 3px;
            color: #64748b;
            font-size: .9rem;
        }

        .visit-report-meta {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0;
            margin-bottom: 18px;
            overflow: hidden;
            border: 1px solid #dbe4ef;
            border-radius: 12px;
            background: #f8fafc;
        }

        .visit-report-meta-item {
            min-width: 0;
            padding: 10px 12px;
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            gap: 7px;
            align-items: baseline;
            border-right: 1px solid #e2e8f0;
        }

        .visit-report-meta-item:last-child {
            border-right: 0;
        }

        .visit-report-meta-label,
        .visit-report-label {
            color: #475569;
            font-weight: 600;
        }

        .visit-report-meta-label {
            white-space: nowrap;
        }

        .visit-report-meta-value,
        .visit-report-value {
            min-width: 0;
            color: #0f172a;
            overflow-wrap: anywhere;
            white-space: pre-wrap;
        }

        .visit-report-section {
            margin-bottom: 17px;
        }

        .visit-report-section-title {
            margin: 0 0 7px;
            padding: 7px 10px;
            color: #1e3a8a;
            background: #eff6ff;
            border-left: 4px solid #2563eb;
            border-radius: 0 8px 8px 0;
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.35;
        }

        .visit-report-lines {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            column-gap: 22px;
            row-gap: 0;
        }

        .visit-report-line {
            min-width: 0;
            min-height: 36px;
            padding: 7px 2px;
            display: grid;
            grid-template-columns: 165px minmax(0, 1fr);
            column-gap: 10px;
            align-items: start;
            border-bottom: 1px dotted #d8e0ea;
        }

        .visit-report-line.is-residence {
            grid-template-columns: 175px minmax(0, 1fr);
        }

        .visit-report-line.is-residence .visit-report-value {
            min-height: 24px;
            display: flex;
            align-items: center;
            white-space: normal;
        }

        .visit-report-line.full {
            grid-column: 1 / -1;
        }

        .visit-report-inline-badge {
            min-height: 24px;
            padding: 2px 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #1d4ed8;
            background: #eef4ff;
            border: 1px solid #dbeafe;
            border-radius: 999px;
            font-size: .86rem;
            font-weight: 600;
            line-height: 1.2;
        }

        .visit-report-empty {
            padding: 8px 0;
            color: #64748b;
        }

        .visit-report-image-section {
            margin-top: 20px;
        }

        .visit-report-pictures {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            align-items: start;
        }

        .visit-report-picture-item {
            min-width: 0;
            margin: 0;
        }

        .visit-report-picture-frame {
            overflow: hidden;
            background: #f8fafc;
            border: 1px solid #cfd9e6;
            border-radius: 12px;
        }

        .visit-report-picture-image-wrap {
            height: 235px;
            padding: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
        }

        .visit-report-picture-frame img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: contain;
            object-position: center;
        }

        .visit-report-picture-caption {
            padding: 7px 10px;
            color: #334155;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            font-size: .86rem;
            text-align: center;
        }

        .visit-report-pictures.is-single {
            grid-template-columns: minmax(0, 720px);
            justify-content: center;
        }

        .visit-report-pictures.is-single .visit-report-picture-image-wrap {
            height: 440px;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px 7px;
            }

            .visit-report-toolbar,
            .visit-report-content {
                padding-left: 14px;
                padding-right: 14px;
            }

            .visit-report-print-note {
                width: 100%;
            }

            .visit-report-meta,
            .visit-report-lines,
            .visit-report-pictures {
                grid-template-columns: 1fr;
            }

            .visit-report-meta-item {
                border-right: 0;
                border-bottom: 1px solid #e2e8f0;
            }

            .visit-report-meta-item:last-child {
                border-bottom: 0;
            }

            .visit-report-line {
                grid-template-columns: 145px minmax(0, 1fr);
            }

            .visit-report-pictures.is-single .visit-report-picture-image-wrap,
            .visit-report-picture-image-wrap {
                height: 300px;
            }
        }

        @media (max-width: 480px) {
            .visit-report-line {
                grid-template-columns: 1fr;
                row-gap: 2px;
            }

            .visit-report-toolbar-actions,
            .visit-report-btn {
                width: 100%;
            }
        }

        @media print {
            html,
            body {
                width: 100%;
                background: #fff !important;
            }

            body {
                padding: 0;
                font-size: 10.5pt;
                line-height: 1.34;
            }

            .visit-report-toolbar {
                display: none !important;
            }

            .visit-report-page,
            .visit-report-paper {
                width: 100%;
                max-width: none;
                margin: 0;
            }

            .visit-report-paper {
                overflow: visible;
                border: 0;
                border-radius: 0;
                box-shadow: none;
            }

            .visit-report-content {
                padding: 0;
            }

            .visit-report-head {
                margin-bottom: 4mm;
                padding-bottom: 3mm;
                border-bottom: 1.2pt solid #94a3b8;
            }

            .visit-report-head-title {
                font-size: 15pt;
            }

            .visit-report-head-subtitle {
                font-size: 9pt;
            }

            .visit-report-meta {
                margin-bottom: 4mm;
                border-radius: 0;
                border-color: #94a3b8;
                background: #f8fafc !important;
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .visit-report-meta-item {
                padding: 2.2mm 2.5mm;
            }

            .visit-report-section {
                margin-bottom: 3.5mm;
            }

            .visit-report-section-title {
                margin-bottom: 1mm;
                padding: 1.6mm 2.2mm;
                color: #111827;
                background: #edf2f7 !important;
                border-left: 3pt solid #64748b;
                border-radius: 0;
                font-size: 11.5pt;
                break-after: avoid-page;
                page-break-after: avoid;
            }

            .visit-report-lines {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                column-gap: 5mm;
            }

            .visit-report-line {
                min-height: 7mm;
                padding: 1.5mm .5mm;
                grid-template-columns: 38mm minmax(0, 1fr);
                column-gap: 2.4mm;
                border-bottom: .5pt dotted #b8c2cf;
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .visit-report-line.is-residence {
                grid-template-columns: 40mm minmax(0, 1fr);
            }

            .visit-report-label,
            .visit-report-meta-label {
                font-weight: 600;
            }

            .visit-report-inline-badge {
                min-height: 5.5mm;
                padding: .5mm 2mm;
                color: #111827;
                background: #f1f5f9 !important;
                border-color: #cbd5e1;
                font-size: 9.5pt;
            }

            .visit-report-image-section {
                margin-top: 3.5mm;
                break-before: auto !important;
                page-break-before: auto !important;
                break-inside: auto;
                page-break-inside: auto;
            }

            .visit-report-image-section .visit-report-section-title {
                break-after: avoid-page;
                page-break-after: avoid;
            }

            .visit-report-pictures {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 4mm;
                break-inside: auto;
                page-break-inside: auto;
            }

            .visit-report-picture-item {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .visit-report-picture-frame {
                border-radius: 0;
                border-color: #94a3b8;
            }

            .visit-report-picture-image-wrap {
                height: 55mm;
                padding: 2mm;
            }

            .visit-report-picture-caption {
                padding: 1.5mm 2mm;
                background: #f8fafc !important;
                font-size: 9.5pt;
            }

            .visit-report-pictures.is-single {
                grid-template-columns: 155mm;
                justify-content: center;
            }

            .visit-report-pictures.is-single .visit-report-picture-image-wrap {
                height: 82mm;
            }

            .visit-report-picture-frame img {
                object-fit: contain;
            }
        }
    </style>
</head>
<body>
    <div class="visit-report-page">
        <div class="visit-report-paper">
            <div class="visit-report-toolbar">
                <div class="visit-report-toolbar-actions">
                    <a href="{{ route('vitsitFamily.edit', $visitFamily->id) }}"
                       class="visit-report-btn">
                        ← กลับหน้าข้อมูล
                    </a>

                    <button type="button"
                            class="visit-report-btn visit-report-btn-primary"
                            onclick="printVisitReport(this)">
                        พิมพ์รายงาน
                    </button>
                </div>

                <div class="visit-report-print-note">
                    เพื่อไม่ให้วันที่ URL และเลขหน้าจากเบราว์เซอร์ติดบนกระดาษ กรุณาปิด “ส่วนหัวและส่วนท้าย” ในหน้าต่างพิมพ์
                </div>
            </div>

            <main class="visit-report-content">
                <header class="visit-report-head">
                    <h1 class="visit-report-head-title">รายงานการเยี่ยมบ้าน</h1>
                    <div class="visit-report-head-subtitle">ข้อมูลการเยี่ยมบ้านและการประเมินสภาพเด็กและครอบครัว</div>
                </header>

                <section class="visit-report-meta" aria-label="ข้อมูลผู้รับบริการ">
                    <div class="visit-report-meta-item">
                        <span class="visit-report-meta-label">ชื่อ-สกุล:</span>
                        <span class="visit-report-meta-value">{{ $fullName }}</span>
                    </div>

                    <div class="visit-report-meta-item">
                        <span class="visit-report-meta-label">อายุ:</span>
                        <span class="visit-report-meta-value">{{ $age !== null ? $age . ' ปี' : '-' }}</span>
                    </div>

                    <div class="visit-report-meta-item">
                        <span class="visit-report-meta-label">วันที่เยี่ยม:</span>
                        <span class="visit-report-meta-value">{{ $thaiDate($visitFamily->visit_date ?? null) }}</span>
                    </div>
                </section>

                <section class="visit-report-section">
                    <h2 class="visit-report-section-title">ข้อมูลทั่วไป</h2>
                    <div class="visit-report-lines">
                        <div class="visit-report-line">
                            <div class="visit-report-label">ผู้ให้ข้อมูล</div>
                            <div class="visit-report-value">{{ $visitFamily->family_fname ?: '-' }}</div>
                        </div>

                        <div class="visit-report-line">
                            <div class="visit-report-label">อายุผู้ให้ข้อมูล</div>
                            <div class="visit-report-value">{{ $visitFamily->family_age !== null ? $visitFamily->family_age . ' ปี' : '-' }}</div>
                        </div>

                        <div class="visit-report-line">
                            <div class="visit-report-label">ความสัมพันธ์กับผู้รับ</div>
                            <div class="visit-report-value">{{ $visitFamily->member ?: '-' }}</div>
                        </div>

                        <div class="visit-report-line">
                            <div class="visit-report-label">รายได้เฉลี่ยครอบครัว</div>
                            <div class="visit-report-value">{{ $visitFamily->income->income_name ?? '-' }}</div>
                        </div>

                        <div class="visit-report-line is-residence">
                            <div class="visit-report-label">สถานะการอยู่อาศัย</div>
                            <div class="visit-report-value">
                                @if($visitFamily->residence_status)
                                    <span class="visit-report-inline-badge">{{ $visitFamily->residence_status }}</span>
                                @else
                                    -
                                @endif
                            </div>
                        </div>

                        <div class="visit-report-line">
                            <div class="visit-report-label">โทรศัพท์</div>
                            <div class="visit-report-value">{{ $visitFamily->phone ?: '-' }}</div>
                        </div>
                    </div>
                </section>

                <section class="visit-report-section">
                    <h2 class="visit-report-section-title">ข้อมูลที่อยู่</h2>
                    <div class="visit-report-lines">
                        <div class="visit-report-line">
                            <div class="visit-report-label">เลขที่</div>
                            <div class="visit-report-value">{{ $visitFamily->address ?: '-' }}</div>
                        </div>

                        <div class="visit-report-line">
                            <div class="visit-report-label">หมู่ที่</div>
                            <div class="visit-report-value">{{ $visitFamily->moo ?: '-' }}</div>
                        </div>

                        <div class="visit-report-line">
                            <div class="visit-report-label">ตรอก / ซอย</div>
                            <div class="visit-report-value">{{ $visitFamily->soi ?: '-' }}</div>
                        </div>

                        <div class="visit-report-line">
                            <div class="visit-report-label">ถนน</div>
                            <div class="visit-report-value">{{ $visitFamily->road ?: '-' }}</div>
                        </div>

                        <div class="visit-report-line">
                            <div class="visit-report-label">หมู่บ้าน</div>
                            <div class="visit-report-value">{{ $visitFamily->village ?: '-' }}</div>
                        </div>

                        <div class="visit-report-line">
                            <div class="visit-report-label">รหัสไปรษณีย์</div>
                            <div class="visit-report-value">{{ $visitFamily->zipcode ?: '-' }}</div>
                        </div>

                        <div class="visit-report-line">
                            <div class="visit-report-label">ตำบล / แขวง</div>
                            <div class="visit-report-value">{{ $subDistrictName ?? '-' }}</div>
                        </div>

                        <div class="visit-report-line">
                            <div class="visit-report-label">อำเภอ / เขต</div>
                            <div class="visit-report-value">{{ $districtName ?? '-' }}</div>
                        </div>

                        <div class="visit-report-line full">
                            <div class="visit-report-label">จังหวัด</div>
                            <div class="visit-report-value">{{ $provinceName ?? '-' }}</div>
                        </div>
                    </div>
                </section>

                <section class="visit-report-section">
                    <h2 class="visit-report-section-title">สภาพบ้านและสภาพแวดล้อม</h2>
                    <div class="visit-report-lines">
                        <div class="visit-report-line full">
                            <div class="visit-report-label">สภาพที่อยู่ภายนอก</div>
                            <div class="visit-report-value">{{ $visitFamily->outside_address ?: '-' }}</div>
                        </div>

                        <div class="visit-report-line full">
                            <div class="visit-report-label">สภาพที่อยู่ภายใน</div>
                            <div class="visit-report-value">{{ $visitFamily->inside_address ?: '-' }}</div>
                        </div>

                        <div class="visit-report-line full">
                            <div class="visit-report-label">สภาพแวดล้อม</div>
                            <div class="visit-report-value">{{ $visitFamily->environment ?: '-' }}</div>
                        </div>

                        <div class="visit-report-line full">
                            <div class="visit-report-label">ความสัมพันธ์กับเพื่อนบ้าน</div>
                            <div class="visit-report-value">{{ $visitFamily->neighbor ?: '-' }}</div>
                        </div>

                        <div class="visit-report-line full">
                            <div class="visit-report-label">ความสัมพันธ์ของสมาชิกในบ้าน</div>
                            <div class="visit-report-value">{{ $visitFamily->member_relation ?: '-' }}</div>
                        </div>
                    </div>
                </section>

                <section class="visit-report-section">
                    <h2 class="visit-report-section-title">การประเมินและการช่วยเหลือ</h2>
                    <div class="visit-report-lines">
                        <div class="visit-report-line full">
                            <div class="visit-report-label">ปัญหาที่พบ</div>
                            <div class="visit-report-value">{{ $visitFamily->problem ?: '-' }}</div>
                        </div>

                        <div class="visit-report-line full">
                            <div class="visit-report-label">ความต้องการ</div>
                            <div class="visit-report-value">{{ $visitFamily->need ?: '-' }}</div>
                        </div>

                        <div class="visit-report-line full">
                            <div class="visit-report-label">การวินิจฉัยปัญหา</div>
                            <div class="visit-report-value">{{ $visitFamily->diagnose ?: '-' }}</div>
                        </div>

                        <div class="visit-report-line full">
                            <div class="visit-report-label">การให้ความช่วยเหลือ</div>
                            <div class="visit-report-value">{{ $visitFamily->assistance ?: '-' }}</div>
                        </div>

                        <div class="visit-report-line full">
                            <div class="visit-report-label">ข้อคิดเห็น</div>
                            <div class="visit-report-value">{{ $visitFamily->comment ?: '-' }}</div>
                        </div>

                        <div class="visit-report-line full">
                            <div class="visit-report-label">สิ่งที่ควรแก้ไข</div>
                            <div class="visit-report-value">{{ $visitFamily->modify ?: '-' }}</div>
                        </div>

                        <div class="visit-report-line full">
                            <div class="visit-report-label">หมายเหตุ</div>
                            <div class="visit-report-value">{{ $visitFamily->remark ?: '-' }}</div>
                        </div>
                    </div>
                </section>

                <section class="visit-report-section">
                    <h2 class="visit-report-section-title">ผู้ติดตามเยี่ยมบ้าน</h2>
                    <div class="visit-report-lines">
                        <div class="visit-report-line full">
                            <div class="visit-report-label">ผู้ติดตาม / ผู้บันทึก</div>
                            <div class="visit-report-value">{{ $visitFamily->teacher ?: '-' }}</div>
                        </div>
                    </div>
                </section>

                <section class="visit-report-section visit-report-image-section">
                    <h2 class="visit-report-section-title">ภาพประกอบการเยี่ยมบ้าน</h2>

                    @if($imageCount > 0)
                        <div class="visit-report-pictures {{ $imageCount === 1 ? 'is-single' : '' }}">
                            @foreach($visitFamily->images as $index => $img)
                                <figure class="visit-report-picture-item">
                                    <div class="visit-report-picture-frame">
                                        <div class="visit-report-picture-image-wrap">
                                            <img src="{{ route('vitsitFamily.image.view', $img->id) }}"
                                                 alt="รูปภาพประกอบการเยี่ยมบ้าน {{ $index + 1 }}">
                                        </div>
                                        <figcaption class="visit-report-picture-caption">
                                            รูปภาพประกอบ {{ $index + 1 }}
                                        </figcaption>
                                    </div>
                                </figure>
                            @endforeach
                        </div>
                    @else
                        <div class="visit-report-empty">ไม่มีรูปภาพประกอบ</div>
                    @endif
                </section>
            </main>
        </div>
    </div>

    <script>
        async function printVisitReport(button) {
            const originalText = button?.textContent ?? 'พิมพ์รายงาน';

            if (button) {
                button.disabled = true;
                button.textContent = 'กำลังเตรียมรูปภาพ...';
            }

            try {
                const images = Array.from(document.querySelectorAll('.visit-report-picture-frame img'));

                await Promise.all(images.map((image) => {
                    if (image.complete) {
                        return Promise.resolve();
                    }

                    return new Promise((resolve) => {
                        const done = () => resolve();
                        image.addEventListener('load', done, { once: true });
                        image.addEventListener('error', done, { once: true });
                        window.setTimeout(done, 5000);
                    });
                }));

                window.print();
            } finally {
                if (button) {
                    button.disabled = false;
                    button.textContent = originalText;
                }
            }
        }
    </script>
</body>
</html>