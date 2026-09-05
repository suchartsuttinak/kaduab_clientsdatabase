<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ยังไม่มีเมนูที่ได้รับสิทธิ์ | ระบบจัดการข้อมูลผู้รับบริการ</title>

    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon.ico') }}">
    <link href="{{ asset('backend/assets/css/app.min.css') }}" rel="stylesheet">
    <link href="{{ asset('backend/assets/css/icons.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --access-bg: #f4f7fb;
            --access-surface: #ffffff;
            --access-text: #172033;
            --access-muted: #667085;
            --access-border: #e4eaf2;
            --access-primary: #2563eb;
            --access-primary-soft: #eef5ff;
            --access-warning: #a15c00;
            --access-warning-soft: #fff7e8;
            --access-success: #12715b;
            --access-success-soft: #ecfdf5;
        }

        * { box-sizing: border-box; }

        html, body { min-height: 100%; }

        body {
            margin: 0;
            font-family: 'Kanit', sans-serif;
            color: var(--access-text);
            background:
                radial-gradient(circle at 12% 0%, rgba(37, 99, 235, .08), transparent 28%),
                radial-gradient(circle at 92% 8%, rgba(14, 165, 233, .07), transparent 24%),
                var(--access-bg);
        }

        .access-shell {
            min-height: 100vh;
            display: grid;
            grid-template-rows: auto 1fr auto;
        }

        .access-topbar {
            height: 76px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 0 clamp(20px, 4vw, 64px);
            background: rgba(255, 255, 255, .94);
            border-bottom: 1px solid var(--access-border);
            backdrop-filter: blur(10px);
        }

        .access-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .access-brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 13px;
            display: grid;
            place-items: center;
            background: var(--access-primary-soft);
            color: var(--access-primary);
            font-size: 20px;
            flex: 0 0 auto;
        }

        .access-brand strong {
            display: block;
            font-size: 15px;
            font-weight: 600;
            line-height: 1.25;
        }

        .access-brand small {
            display: block;
            margin-top: 2px;
            color: var(--access-muted);
            font-size: 12px;
        }

        .access-user {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--access-muted);
            font-size: 13px;
            white-space: nowrap;
        }

        .access-user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px var(--access-border);
        }

        .access-main {
            width: min(960px, calc(100% - 32px));
            margin: 0 auto;
            padding: clamp(46px, 8vh, 92px) 0 44px;
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }

        .access-card {
            width: 100%;
            background: var(--access-surface);
            border: 1px solid var(--access-border);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, .08);
            overflow: hidden;
        }

        .access-card-head {
            padding: clamp(28px, 5vw, 48px);
            padding-bottom: 30px;
            text-align: center;
            border-bottom: 1px solid var(--access-border);
        }

        .access-status-icon {
            width: 76px;
            height: 76px;
            margin: 0 auto 20px;
            border-radius: 22px;
            display: grid;
            place-items: center;
            background: var(--access-warning-soft);
            color: var(--access-warning);
            font-size: 36px;
        }

        .access-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 12px;
            margin-bottom: 14px;
            border-radius: 999px;
            background: var(--access-success-soft);
            color: var(--access-success);
            font-size: 12px;
            font-weight: 500;
        }

        .access-title {
            margin: 0;
            font-size: clamp(25px, 3vw, 34px);
            font-weight: 600;
            letter-spacing: -.02em;
        }

        .access-description {
            max-width: 690px;
            margin: 13px auto 0;
            color: var(--access-muted);
            font-size: 15px;
            line-height: 1.8;
        }

        .access-body {
            padding: clamp(24px, 4vw, 38px) clamp(24px, 5vw, 48px) 40px;
        }

        .access-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 24px;
        }

        .access-meta {
            padding: 17px 18px;
            background: #f8fafc;
            border: 1px solid var(--access-border);
            border-radius: 16px;
            min-width: 0;
        }

        .access-meta-label {
            color: var(--access-muted);
            font-size: 11px;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .access-meta-wide { grid-column: span 2; }

        .access-meta-value {
            color: var(--access-text);
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .access-note {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 17px 18px;
            border-radius: 16px;
            background: var(--access-primary-soft);
            color: #244a86;
            line-height: 1.7;
            font-size: 13px;
        }

        .access-note i {
            margin-top: 2px;
            font-size: 18px;
            color: var(--access-primary);
        }

        .access-actions {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 28px;
        }

        .access-btn {
            min-height: 44px;
            padding: 10px 18px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: transform .15s ease, box-shadow .15s ease, background-color .15s ease;
        }

        .access-btn:hover { transform: translateY(-1px); }

        .access-btn-primary {
            border: 1px solid var(--access-primary);
            background: var(--access-primary);
            color: #fff;
            box-shadow: 0 8px 20px rgba(37, 99, 235, .18);
        }

        .access-btn-light {
            border: 1px solid var(--access-border);
            background: #fff;
            color: #344054;
        }

        .access-footer {
            padding: 18px 24px 24px;
            text-align: center;
            color: #98a2b3;
            font-size: 11px;
        }

        @media (max-width: 760px) {
            .access-topbar { height: auto; min-height: 70px; padding-top: 12px; padding-bottom: 12px; }
            .access-brand small { display: none; }
            .access-user span { display: none; }
            .access-main { padding-top: 32px; }
            .access-grid { grid-template-columns: 1fr; }
            .access-meta-wide { grid-column: auto; }
            .access-card { border-radius: 18px; }
        }
    </style>
</head>
<body>
@php
    $projectNames = $user->projects
        ->map(fn ($project) => $project->project_name ?? $project->name)
        ->filter()
        ->values();

    if ($projectNames->isEmpty() && $user->project) {
        $projectNames = collect([$user->project->project_name ?? $user->project->name])->filter()->values();
    }

    $projectText = $user->isAdmin()
        ? 'ทุกหน่วยงาน (Admin)'
        : ($projectNames->isNotEmpty() ? $projectNames->implode(', ') : 'ทุกหน่วยงาน');

    $houseNames = $user->houses
        ->map(fn ($house) => $house->house_name ?? $house->name)
        ->filter()
        ->values();
    $houseText = $user->isAdmin()
        ? 'ทุกบ้าน (Admin)'
        : ($houseNames->isNotEmpty() ? $houseNames->implode(', ') : 'ทุกบ้าน');
@endphp

<div class="access-shell">
    <header class="access-topbar">
        <div class="access-brand">
            <div class="access-brand-mark"><i class="mdi mdi-shield-account-outline"></i></div>
            <div>
                <strong>ระบบจัดการข้อมูลผู้รับบริการ</strong>
                <small>การเข้าถึงระบบตามสิทธิ์ที่ได้รับมอบหมาย</small>
            </div>
        </div>

        <div class="access-user">
            <img src="{{ $user->photo_url }}" class="access-user-avatar" alt="รูปผู้ใช้งาน">
            <span>{{ $user->name }}</span>
        </div>
    </header>

    <main class="access-main">
        <section class="access-card" aria-labelledby="access-title">
            <div class="access-card-head">
                <div class="access-status-icon"><i class="mdi mdi-account-key-outline"></i></div>
                <div class="access-badge"><i class="mdi mdi-check-circle-outline"></i> เข้าสู่ระบบสำเร็จ</div>
                <h1 class="access-title" id="access-title">ยังไม่มีเมนูที่ได้รับสิทธิ์ให้เข้าใช้งาน</h1>
                <p class="access-description">
                    บัญชีของคุณเข้าสู่ระบบได้ตามปกติ แต่ยังไม่มีเมนูหรือฟอร์มที่ได้รับสิทธิ์ใช้งาน
                    ระบบจึงแสดงหน้านี้แทนการพาไปยังหน้าที่ไม่ได้รับอนุญาต
                </p>
            </div>

            <div class="access-body">
                <div class="access-grid">
                    <div class="access-meta">
                        <div class="access-meta-label">ผู้ใช้งาน</div>
                        <div class="access-meta-value" title="{{ $user->name }}">{{ $user->name }}</div>
                    </div>
                    <div class="access-meta">
                        <div class="access-meta-label">บทบาท</div>
                        <div class="access-meta-value">{{ $user->role_label }}</div>
                    </div>
                    <div class="access-meta">
                        <div class="access-meta-label">สถานะบัญชี</div>
                        <div class="access-meta-value">{{ $user->status_label }}</div>
                    </div>
                    <div class="access-meta">
                        <div class="access-meta-label">หน่วยงาน / โครงการ</div>
                        <div class="access-meta-value" title="{{ $projectText }}">{{ $projectText }}</div>
                    </div>
                    <div class="access-meta access-meta-wide">
                        <div class="access-meta-label">บ้าน / สถานที่พักพิงที่ได้รับมอบหมาย</div>
                        <div class="access-meta-value" title="{{ $houseText }}">{{ $houseText }}</div>
                    </div>
                </div>

                <div class="access-note">
                    <i class="mdi mdi-information-outline"></i>
                    <div>
                        กรุณาให้ผู้ดูแลระบบเลือก <strong>สิทธิ์รายฟอร์ม/เมนู</strong> ที่ต้องการให้บัญชีนี้ใช้งาน
                        จากนั้นกด “ตรวจสอบสิทธิ์อีกครั้ง” ได้ทันที โดยขอบเขตข้อมูลจะใช้กติกา
                        <strong>ไม่เลือกหน่วยงาน = ทุกหน่วยงาน</strong> และ <strong>ไม่เลือกบ้าน = ทุกบ้าน</strong>
                    </div>
                </div>

                <div class="access-actions">
                    <a href="{{ route('access.no_permissions') }}" class="access-btn access-btn-primary">
                        <i class="mdi mdi-refresh"></i> ตรวจสอบสิทธิ์อีกครั้ง
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="m-0" data-permission-action="navigation">
                        @csrf
                        <button type="submit" class="access-btn access-btn-light">
                            <i class="mdi mdi-logout-variant"></i> ออกจากระบบ
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <footer class="access-footer">
        เมนูและการกระทำกำหนดด้วยสิทธิ์รายฟอร์ม ส่วนหน่วยงาน/โครงการและบ้านใช้จำกัดขอบเขตข้อมูลที่มองเห็น
    </footer>
</div>
</body>
</html>
