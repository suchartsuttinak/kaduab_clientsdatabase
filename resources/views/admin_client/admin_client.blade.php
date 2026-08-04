<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'ระบบข้อมูลผู้รับบริการ')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ระบบฐานข้อมูลผู้รับบริการ" />
    <meta name="author" content="Zoyothemes" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon.ico') }}">

    <link href="{{ asset('backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />
    <link href="{{ asset('backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">

    <link href="{{ asset('backend/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}"
        rel="stylesheet" />
    <link href="{{ asset('backend/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}"
        rel="stylesheet" />
    <link href="{{ asset('backend/assets/libs/datatables.net-keytable-bs5/css/keyTable.bootstrap5.min.css') }}"
        rel="stylesheet" />
    <link href="{{ asset('backend/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}"
        rel="stylesheet" />
    <link href="{{ asset('backend/assets/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css') }}"
        rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
        rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @stack('styles')

    <style>
        :root {
            --topbar-height: 72px;
            --sidebar-width: 260px;
            --topbar-offset: 0px;
            --content-top-padding: .75rem;
            --content-top-padding-lg: .75rem;
            --content-top-padding-sm: .5rem;
            --topbar-bg: rgba(255, 255, 255, .92);
            --topbar-border: #e6edf5;
            --topbar-text: #1f3b64;
            --topbar-muted: #6b7280;
            --topbar-hover: #eef5ff;
            --topbar-active-bg: #e8f1ff;
            --topbar-active-text: #2563eb;
            --topbar-shadow: 0 6px 20px rgba(15, 23, 42, .06);
            --content-bg: #f8fafc;
            --card-border: #e9ecef;
            --shadow-soft: 0 6px 18px rgba(15, 23, 42, .04);
            --radius-lg: 16px;
            --radius-md: 12px;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }

        body {
            margin: 0;
            font-family: 'Kanit', sans-serif;
            font-size: 15px;
            line-height: 1.6;
            color: #212529;
            background: var(--content-bg);
            padding-top: var(--topbar-height) !important;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        .card-header,
        .navbar-brand {
            font-weight: 500;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        table td,
        table th {
            font-size: 14px;
            vertical-align: middle;
        }

        .btn {
            font-weight: 500;
            letter-spacing: .15px;
        }

        .form-control,
        .form-select {
            color: #495057;
            border-radius: 12px;
            min-height: 42px;
            max-width: 100%;
        }

        .form-control::placeholder {
            color: #adb5bd;
            opacity: 1;
        }

        table.dataTable td,
        table.dataTable th {
            font-size: 12px;
        }

        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_paginate {
            font-size: 12px;
        }

        .dataTables_wrapper .dataTables_filter input {
            margin-left: .4rem;
        }

        #app-layout {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            max-width: 100%;
            overflow-x: hidden;
        }

        .app-body {
            display: flex;
            min-height: calc(100vh - var(--topbar-height));
            max-width: 100%;
            overflow-x: hidden;
        }

        /* Sidebar wrapper: สำคัญมาก ใช้ครอบ include เพื่อกัน sidebar ซ้อนบน iPad */
        .client-sidebar-panel {
            width: var(--sidebar-width);
            flex: 0 0 var(--sidebar-width);
            background: #f8f9fa;
            border-right: 1px solid #dee2e6;
            min-height: calc(100vh - var(--topbar-height));
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
        }

        .client-sidebar-panel .app-sidebar-menu,
        .client-sidebar-panel .left-side-menu,
        .client-sidebar-panel .admin-sidebar {
            width: 100% !important;
            max-width: 100% !important;
            min-height: 100%;
            position: static !important;
            transform: none !important;
            visibility: visible !important;
            pointer-events: auto !important;
            box-shadow: none !important;
            border-right: 0 !important;
        }

        .content-page,
        .main-content {
            flex: 1 1 auto;
            min-width: 0;
            max-width: 100%;
            position: relative;
            background-color: var(--content-bg);
            min-height: calc(100vh - var(--topbar-height));
            overflow-x: hidden;
            margin-top: 0 !important;
            padding: var(--content-top-padding) 1.25rem 1.5rem 1.25rem !important;
        }

        .content-page>.content,
        .page-content,
        .main-content,
        .content-shell {
            max-width: 100%;
            min-width: 0;
        }

        .content-shell,
        .content-scroll-x {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        /*
         * ล้างเฉพาะระยะด้านบนของ wrapper ชั้นนอก
         * ไม่แตะ padding ภายในการ์ด เพื่อไม่ให้หัวข้อชิดกรอบ
         */
        .content-scroll-x > .container,
        .content-scroll-x > .container-fluid,
        .content-scroll-x > .page-content,
        .content-scroll-x > .page-wrapper,
        .content-scroll-x > .main-wrapper,
        .content-scroll-x > .form-wrap-wide {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        .content-scroll-x > :first-child {
            margin-top: 0 !important;
        }

        .content-scroll-x > :first-child.mt-1,
        .content-scroll-x > :first-child.mt-2,
        .content-scroll-x > :first-child.mt-3,
        .content-scroll-x > :first-child.mt-4,
        .content-scroll-x > :first-child.mt-5,
        .content-scroll-x > :first-child.pt-1,
        .content-scroll-x > :first-child.pt-2,
        .content-scroll-x > :first-child.pt-3,
        .content-scroll-x > :first-child.pt-4,
        .content-scroll-x > :first-child.pt-5,
        .content-scroll-x > :first-child.py-1,
        .content-scroll-x > :first-child.py-2,
        .content-scroll-x > :first-child.py-3,
        .content-scroll-x > :first-child.py-4,
        .content-scroll-x > :first-child.py-5 {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        .content-scroll-x {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
            overflow-y: visible;
        }

        .content-page> :first-child,
        .content-page>.content-shell:first-child,
        .content-page>.content-shell>.content-scroll-x:first-child,
        .content-page>.content-shell>.content-scroll-x> :first-child,
        .content-page .container:first-child,
        .content-page .container-fluid:first-child,
        .content-page .row:first-child,
        .content-page .card:first-child,
        .content-page section:first-child,
        .content-page article:first-child,
        .content-page form:first-child,
        .content-page .page-header-compact:first-child {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        .content-page .container-fluid.mt-1,
        .content-page .container-fluid.mt-2,
        .content-page .container-fluid.mt-3,
        .content-page .container.mt-1,
        .content-page .container.mt-2,
        .content-page .container.mt-3,
        .content-page .row.mt-1,
        .content-page .row.mt-2,
        .content-page .row.mt-3,
        .content-page .card.mt-1,
        .content-page .card.mt-2,
        .content-page .card.mt-3 {
            margin-top: 0 !important;
        }

        .table-wrap,
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table-wrap table {
            min-width: 900px;
        }

        .form-wrap-wide {
            width: 100%;
            overflow-x: hidden;
            padding-bottom: .5rem;
        }

        .card {
            border: 1px solid var(--card-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            max-width: 100%;
            min-width: 0;
        }

        .card-body,
        form {
            max-width: 100%;
            min-width: 0;
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #eef1f4;
        }

        #appTopbar,
        .app-topbar,
        .topbar-custom {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            margin-top: 0 !important;
            transform: none !important;
            z-index: 1100;
            min-height: var(--topbar-height);
            background: var(--topbar-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--topbar-border);
            box-shadow: var(--topbar-shadow);
        }

        .topbar-navbar {
            min-height: var(--topbar-height);
            padding-top: .5rem;
            padding-bottom: .5rem;
        }

        .topbar-left-group {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .topbar-brand {
            display: inline-flex;
            align-items: center;
            gap: .65rem;
            text-decoration: none;
            margin-left: .35rem;
            min-width: 0;
        }

        .topbar-brand-badge {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #2563eb, #60a5fa);
            color: #fff;
            font-size: 15px;
            box-shadow: 0 4px 10px rgba(37, 99, 235, .25);
            flex: 0 0 36px;
        }

        .topbar-brand-text {
            font-weight: 600;
            color: #1e3a5f;
            font-size: 15px;
            letter-spacing: .01em;
            white-space: nowrap;
            flex: 0 0 auto;
        }

        .topbar-toggler {
            border: 0;
            box-shadow: none !important;
            padding: .4rem .55rem;
            border-radius: 10px;
        }

        .topbar-toggler:hover {
            background: #f4f8ff;
        }

        .topbar-sidebar-toggle {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            color: var(--topbar-text);
            align-items: center;
            justify-content: center;
            transition: all .2s ease;
        }

        .topbar-sidebar-toggle:hover {
            background: #f4f8ff;
            color: var(--topbar-active-text);
        }

        .topbar-icon {
            width: 18px;
            height: 18px;
        }

        .topbar-collapse {
            align-items: center;
        }

        .topbar-menu {
            gap: .35rem;
        }

        .topbar-link {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            color: var(--topbar-text) !important;
            font-weight: 500;
            padding: .65rem .9rem !important;
            border-radius: 12px;
            transition: all .2s ease;
            white-space: nowrap;
            position: relative;
        }

        .topbar-link i {
            font-size: 14px;
            width: 16px;
            text-align: center;
        }

        .topbar-link:hover,
        .topbar-link:focus {
            background: var(--topbar-hover);
            color: var(--topbar-active-text) !important;
        }

        .topbar-link.active {
            background: var(--topbar-active-bg);
            color: var(--topbar-active-text) !important;
            font-weight: 600;
        }

        .topbar-link.dropdown-toggle::after {
            margin-left: .45rem;
            opacity: .7;
        }

        .topbar-profile-nav {
            margin-top: 0;
        }

        .topbar-user {
            display: inline-flex;
            align-items: center;
            gap: .7rem;
            padding: .4rem .6rem .4rem .45rem !important;
            border-radius: 14px;
            color: var(--topbar-text) !important;
            transition: all .2s ease;
        }

        .topbar-user:hover,
        .topbar-user:focus {
            background: #f7faff;
        }

        .topbar-user-avatar {
            width: 42px;
            height: 42px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #dbeafe;
            box-shadow: 0 2px 8px rgba(37, 99, 235, .12);
        }

        .topbar-user-meta {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
        }

        .topbar-user-label {
            font-size: 11px;
            color: var(--topbar-muted);
        }

        .topbar-user-name {
            font-size: 14px;
            font-weight: 600;
            color: #1f2937;
        }

        .topbar-dropdown {
            min-width: 235px;
            padding: .45rem;
            border: 1px solid #e8edf3;
            border-radius: 14px;
            box-shadow: 0 14px 30px rgba(15, 23, 42, .10);
            z-index: 1200;
        }

        .topbar-dropdown .dropdown-header {
            color: #64748b;
            font-size: 12px;
            font-weight: 600;
            padding: .55rem .75rem;
        }

        .topbar-dropdown .dropdown-item {
            display: flex;
            align-items: center;
            border-radius: 10px;
            padding: .65rem .8rem;
            color: #334155;
            transition: all .18s ease;
            font-size: 14px;
        }

        .topbar-dropdown .dropdown-item:hover,
        .topbar-dropdown .dropdown-item:focus {
            background: #eef5ff;
            color: #2563eb;
        }

        .topbar-dropdown .dropdown-divider {
            margin: .4rem 0;
        }

        .button-toggle-menu {
            cursor: pointer;
            outline: none;
            box-shadow: none !important;
        }

        .button-toggle-menu.nav-link {
            display: flex;
            align-items: center;
            border: 0;
            background: transparent;
        }

        .client-sidebar-toggle {
    cursor: pointer;
    outline: none;
    box-shadow: none !important;
}

.client-sidebar-toggle:focus-visible {
    outline: 3px solid rgba(37, 99, 235, .22);
    outline-offset: 2px;
}

.client-sidebar-toggle[aria-expanded="false"] {
    background: #e8f1ff !important;
    color: #2563eb !important;
}

        .nav-user .pro-user-name {
            color: #333 !important;
            font-weight: 500;
        }

        .nav-user img,
        .profile-avatar {
            border: 2px solid #0d6efd;
            width: 40px;
            height: 40px;
            object-fit: cover;
        }

        .client-mini-card {
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        }

        .client-id {
            line-height: 1;
        }

        #sidebar-menu {
            padding-bottom: 1rem;
        }

        #side-menu {
            padding-left: 0;
            margin-bottom: 0;
        }

        #side-menu .menu-title {
            font-size: 12px;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: .04em;
            padding: 12px 20px 8px;
            margin: 0;
        }

        #side-menu>li>a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #334155;
            font-weight: 500;
            padding: 10px 18px;
            margin: 2px 10px;
            border-radius: 10px;
            transition: all .2s ease;
            text-decoration: none;
            position: relative;
        }

        #side-menu>li>a:hover {
            background: #e8f2ff;
            color: #0d6efd;
        }

        #side-menu>li>a.active {
            background: #dbeafe;
            color: #0d6efd;
            font-weight: 600;
        }

        #side-menu>li>a i,
        #side-menu>li>a svg,
        .sidebar-fa-icon {
            width: 18px;
            min-width: 18px;
            text-align: center;
        }

        #side-menu .menu-arrow {
            margin-left: auto;
            transition: transform .2s ease;
        }

        #side-menu>li>a[aria-expanded="true"] .menu-arrow {
            transform: rotate(90deg);
        }

        #side-menu .collapse {
            transition: all .2s ease;
        }

        #side-menu .nav-second-level {
            list-style: none;
            padding: 4px 0 6px 0;
            margin: 0 0 4px 0;
        }

        #side-menu .nav-second-level li a {
            display: block;
            color: #475569;
            font-size: 14px;
            font-weight: 400;
            padding: 8px 18px 8px 50px;
            margin: 1px 10px;
            border-radius: 8px;
            text-decoration: none;
            transition: all .2s ease;
        }

        #side-menu .nav-second-level li a:hover {
            background: #eef6ff;
            color: #0d6efd;
        }

        #side-menu .nav-second-level li a.active {
            background: #dbeafe;
            color: #0d6efd;
            font-weight: 500;
        }

        .page-header-compact {
            margin-top: 0 !important;
            padding-top: 0 !important;
            margin-bottom: 1rem;
        }

        .page-header-compact .title-wrap,
        .page-header-compact .section-title-wrap {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        .page-header-compact h1,
        .page-header-compact h2,
        .page-header-compact h3,
        .page-header-compact h4 {
            margin-top: 0 !important;
            margin-bottom: .2rem;
        }

        .page-header-compact .page-subtitle {
            margin-bottom: .35rem;
            color: #6c757d;
        }

        .page-header-compact .page-divider {
            width: 380px;
            max-width: 100%;
            height: 1px;
            background: #cfd8ff;
            margin: .5rem auto 0;
        }

        .report-wrap,
        .print-page,
        .report-container {
            max-width: 100%;
        }

        .report-wrap img,
        .print-page img,
        .report-container img {
            max-width: 100%;
            height: auto;
        }

        .app-footer {
            margin-top: 1.25rem;
            padding: .9rem 0 0 0;
            background: transparent;
            border-top: 1px solid #e5e7eb;
        }

        .app-footer .footer-text {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.6;
        }

        .app-footer .fw-semibold {
            color: #374151;
        }

        /*
         * Notebook Topbar Width Fix
         * ช่วง 1200–1399px มีพื้นที่ไม่พอสำหรับชื่อระบบ + เมนูทั้งหมด
         * จึงซ่อนเฉพาะข้อความชื่อระบบ แต่ยังคงไอคอนระบบไว้
         */
        @media (min-width: 1200px) and (max-width: 1399.98px) {
            .topbar-brand-text {
                display: none !important;
            }

            .topbar-brand {
                gap: 0 !important;
                margin-left: 0 !important;
                flex: 0 0 auto !important;
            }

            .topbar-left-group {
                flex: 0 0 auto !important;
                gap: .35rem !important;
            }

            .topbar-collapse {
                min-width: 0 !important;
            }

            .topbar-menu {
                gap: .08rem !important;
            }

            .topbar-link {
                gap: .35rem !important;
                padding: .6rem .62rem !important;
                font-size: 14px !important;
            }

            .topbar-user {
                gap: .45rem !important;
                padding-left: .35rem !important;
                padding-right: .4rem !important;
            }

            .topbar-user-label {
                display: none !important;
            }
        }

        @media (min-width:1200px) {
            .topbar-menu .dropdown:hover>.topbar-dropdown {
                display: block;
                margin-top: 0;
            }

            .navbar .nav-item.dropdown:hover>.dropdown-menu {
                display: block;
                opacity: 1;
                transform: translateY(0);
                margin-top: 0;
            }

            .topbar-custom .dropdown-menu {
                display: none;
                opacity: 0;
                transform: translateY(10px);
                transition: all .25s ease-in-out;
            }

            .topbar-custom .navbar-collapse {
                display: flex !important;
                align-items: center;
            }

            .content-page {
                padding-top: var(--content-top-padding-lg) !important;
            }
        }

        @media (max-width:1199.98px) {
            :root {
                --topbar-offset: 0px;
            }

            .app-body {
                flex-direction: row !important;
            }

            .client-sidebar-panel {
                position: fixed !important;
                top: var(--topbar-height) !important;
                left: 0 !important;
                width: 260px !important;
                max-width: 82vw !important;
                height: calc(100dvh - var(--topbar-height)) !important;
                background: #fff !important;
                z-index: 1090 !important;
                transform: translateX(-100%) !important;
                transition: transform .25s ease, visibility .25s ease !important;
                box-shadow: 2px 0 18px rgba(15, 23, 42, .12) !important;
                border-right: 1px solid #dee2e6 !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
                visibility: hidden !important;
                pointer-events: none !important;
            }

            body.sidebar-open .client-sidebar-panel {
                transform: translateX(0) !important;
                visibility: visible !important;
                pointer-events: auto !important;
            }

            .client-sidebar-panel .app-sidebar-menu,
            .client-sidebar-panel .left-side-menu,
            .client-sidebar-panel .admin-sidebar {
                position: static !important;
                transform: none !important;
                visibility: visible !important;
                pointer-events: auto !important;
                width: 100% !important;
                height: auto !important;
                min-height: 100% !important;
                max-width: 100% !important;
                box-shadow: none !important;
                border-right: 0 !important;
                background: #fff !important;
                overflow: visible !important;
            }

            .sidebar-overlay {
                position: fixed;
                top: var(--topbar-height);
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, .35);
                z-index: 1085;
                opacity: 0;
                visibility: hidden;
                transition: .25s ease;
            }

            body.sidebar-open .sidebar-overlay {
                opacity: 1;
                visibility: visible;
            }

            .content-page {
                width: 100%;
                max-width: 100%;
                margin-left: 0 !important;
                padding: .35rem 1rem 1.25rem 1rem !important;
                overflow-x: hidden;
            }

            #side-menu>li>a {
                margin-left: 8px;
                margin-right: 8px;
            }

            #side-menu .nav-second-level li a {
                padding-left: 46px;
                margin-left: 8px;
                margin-right: 8px;
            }

            .topbar-navbar {
                min-height: auto;
            }

            .topbar-collapse {
                padding-top: .85rem;
                max-height: calc(100dvh - var(--topbar-height) - 12px);
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }

            .topbar-menu,
            .topbar-profile-nav {
                width: 100%;
            }

            .topbar-menu .nav-item,
            .topbar-profile-nav .nav-item {
                width: 100%;
            }

            .topbar-link,
            .topbar-user {
                width: 100%;
                justify-content: flex-start;
            }

            .topbar-dropdown {
                position: static !important;
                transform: none !important;
                width: 100%;
                min-width: 100%;
                margin-top: .3rem;
                border-radius: 12px;
                box-shadow: none;
            }

            .topbar-profile-nav {
                margin-top: .7rem;
                padding-top: .7rem;
                border-top: 1px solid #e9edf3;
            }

            .topbar-user {
                padding-left: .2rem !important;
            }

            .topbar-brand {
                margin-left: .15rem;
            }
        }

        @media (min-width:768px) and (max-width:1199.98px) {
            .content-scroll-x {
                overflow-x: hidden !important;
            }

            .content-scroll-x>* {
                max-width: 100% !important;
            }

            .form-wrap-wide {
                overflow-x: hidden !important;
            }

            .card,
            .card-body,
            form,
            .tab-content,
            .tab-pane {
                max-width: 100% !important;
            }

            .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }

            .row>* {
                min-width: 0 !important;
            }

            .form-control,
            .form-select,
            textarea {
                max-width: 100% !important;
            }

            .nav-tabs,
            .nav-pills {
                flex-wrap: nowrap !important;
                overflow-x: auto !important;
                overflow-y: hidden !important;
                white-space: nowrap !important;
                -webkit-overflow-scrolling: touch;
            }

            .nav-tabs .nav-link,
            .nav-pills .nav-link {
                white-space: nowrap !important;
                flex: 0 0 auto !important;
            }

            .table-wrap,
            .table-responsive,
            .dataTables_wrapper {
                overflow-x: auto !important;
            }

            .modal-dialog {
                max-width: calc(100vw - 40px) !important;
                margin: 20px auto !important;
            }

            .modal-body {
                max-height: calc(100dvh - 160px) !important;
                overflow-y: auto !important;
            }
        }

        @media (max-width:767.98px) {
            :root {
                --topbar-height: 64px;
                --topbar-offset: 0px;
            }

            body {
                font-size: 14px;
            }

            .topbar-brand-text {
                font-size: 14px;
            }

            .topbar-link {
                font-size: .95rem;
                padding: .65rem .8rem !important;
            }

            .topbar-user-avatar,
            .profile-avatar {
                width: 38px;
                height: 38px;
            }

            .topbar-dropdown .dropdown-item {
                font-size: 13.5px;
            }

            .topbar-custom .container-fluid,
            .topbar-custom .container-xxl {
                padding-left: .75rem;
                padding-right: .75rem;
            }

            .content-page {
                padding: var(--content-top-padding-sm) .75rem 1rem .75rem !important;
            }

            .card {
                border-radius: 12px;
            }

            table td,
            table th {
                font-size: 13px;
            }

            .page-header-compact {
                margin-bottom: .75rem;
            }

            .app-footer {
                margin-top: 1rem;
                padding-top: .75rem;
            }

            .app-footer .footer-text {
                font-size: 12px;
            }
        }


        /* =========================================================
   DESKTOP FIX หลังครอบ sidebar ด้วย client-sidebar-panel
   แก้จอใหญ่ไม่ให้ content ถูกดันซ้ำ
========================================================= */
        @media (min-width:1200px) {

            .app-body {
                display: flex !important;
                flex-direction: row !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            .client-sidebar-panel {
                position: relative !important;
                width: var(--sidebar-width) !important;
                flex: 0 0 var(--sidebar-width) !important;
                max-width: var(--sidebar-width) !important;
                min-height: calc(100vh - var(--topbar-height)) !important;
                transform: none !important;
                visibility: visible !important;
                pointer-events: auto !important;
                z-index: auto !important;
            }

            .client-sidebar-panel .app-sidebar-menu,
            .client-sidebar-panel .left-side-menu,
            .client-sidebar-panel .admin-sidebar {
                position: static !important;
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                transform: none !important;
                visibility: visible !important;
                pointer-events: auto !important;
                box-shadow: none !important;
            }

            .content-page,
            .main-content {
                flex: 1 1 auto !important;
                width: auto !important;
                max-width: none !important;
                min-width: 0 !important;
                margin-left: 0 !important;
                padding-left: 1.25rem !important;
                padding-right: 1.25rem !important;
            }

            .content-shell,
            .content-scroll-x {
                width: 100% !important;
                max-width: 100% !important;
            }

            .sidebar-overlay {
                display: none !important;
            }
        }


        /* =========================================================
   iPAD CONTENT WIDTH FIX
   แก้หน้า iPad ถูกบีบแคบ / content เล็ก
========================================================= */
        @media (min-width:768px) and (max-width:1199.98px) {

            .content-page {
                width: 100% !important;
                max-width: 100% !important;
                margin-left: 0 !important;
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            .content-shell,
            .content-scroll-x {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
            }

            .content-scroll-x {
                overflow-x: hidden !important;
            }

            .content-scroll-x>.container,
            .content-scroll-x>.container-fluid,
            .content-page .container,
            .content-page .container-fluid,
            .content-page .container-md,
            .content-page .container-lg,
            .content-page .container-xl,
            .content-page .container-xxl {
                width: 100% !important;
                max-width: 100% !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
            }

            .content-page .card,
            .content-page .table-wrap,
            .content-page .table-responsive,
            .content-page form {
                width: 100% !important;
                max-width: 100% !important;
            }

            .content-page .table-wrap,
            .content-page .table-responsive {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }
        }

        /* =========================================================
   iPAD TOPBAR COLLAPSE FIX
   แก้เมนูบนหัวเว็บซ้อนกับเนื้อหา
========================================================= */
        @media (min-width:768px) and (max-width:1199.98px) {

            /* ปิดเมนู topbar ที่ collapse ค้าง */
            .topbar-collapse:not(.show),
            .navbar-collapse:not(.show) {
                display: none !important;
                height: 0 !important;
                overflow: hidden !important;
                visibility: hidden !important;
                opacity: 0 !important;
                pointer-events: none !important;
            }

            /* เปิดเฉพาะตอนกดเมนูจริง */
            .topbar-collapse.show,
            .navbar-collapse.show {
                display: block !important;
                position: absolute !important;
                top: 100% !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                background: #fff !important;
                z-index: 1110 !important;
                padding: .75rem 1rem !important;
                box-shadow: 0 12px 24px rgba(15, 23, 42, .12) !important;
                border-bottom: 1px solid #e5e7eb !important;
            }

            .topbar-menu,
            .topbar-profile-nav {
                width: 100% !important;
            }

            .topbar-link,
            .topbar-user {
                width: 100% !important;
                justify-content: flex-start !important;
            }

            /* กันเมนู sidebar โผล่ซ้อน */
            body:not(.sidebar-open) .client-sidebar-panel {
                transform: translate3d(-110%, 0, 0) !important;
                pointer-events: none !important;
            }

            body.sidebar-open .client-sidebar-panel {
                transform: translate3d(0, 0, 0) !important;
                pointer-events: auto !important;
            }

            .content-page,
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }


        /* =========================================================
   Desktop / Notebook Sidebar Collapse
   ใช้เฉพาะจอตั้งแต่ 1200px ขึ้นไป
========================================================= */

        .client-sidebar-panel {
            transition:
                width .22s ease,
                max-width .22s ease,
                flex-basis .22s ease,
                opacity .18s ease,
                transform .25s ease;
        }

        @media (min-width: 1200px) {

            body.client-sidebar-collapsed .client-sidebar-panel {
                width: 0 !important;
                min-width: 0 !important;
                max-width: 0 !important;
                flex: 0 0 0 !important;
                overflow: hidden !important;
                border-right: 0 !important;
                opacity: 0;
                visibility: hidden !important;
                pointer-events: none !important;
            }

            body.client-sidebar-collapsed .client-sidebar-panel .app-sidebar-menu {
                width: var(--sidebar-width) !important;
                min-width: var(--sidebar-width) !important;
            }

            body.client-sidebar-collapsed .content-page,
            body.client-sidebar-collapsed .main-content {
                width: 100% !important;
                max-width: 100% !important;
                flex: 1 1 100% !important;
                margin-left: 0 !important;
            }
        }
        
    </style>
</head>

<body data-menu-color="light" data-sidebar="default">

    <div id="app-layout">

        @include('admin_client.body.client_header')

        <div class="app-body">

           <aside id="clientSidebarPanel"
            class="client-sidebar-panel"
            aria-label="เมนูด้านข้าง">
            @include('admin_client.body.client_sidebar')
        </aside>

            <div class="sidebar-overlay"></div>

            <main class="content-page main-content">
                <div class="content-shell">
                    <div class="content-scroll-x">
                        @yield('content')
                    </div>
                </div>

                @include('admin_client.body.client_footer')
            </main>

        </div>
    </div>

    <script src="{{ asset('backend/assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/waypoints/lib/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/jquery.counterup/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/feather-icons/feather.min.js') }}"></script>

    <script src="{{ asset('backend/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="https://apexcharts.com/samples/assets/stock-prices.js"></script>

    <script src="{{ asset('backend/assets/js/pages/analytics-dashboard.init.js') }}"></script>
    <script src="{{ asset('backend/assets/js/app.js') }}"></script>

    <script src="{{ asset('backend/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/pages/datatable.init.js') }}"></script>

    <script>
        $(function() {
            $.extend(true, $.fn.dataTable.defaults, {
                responsive: false,
                destroy: true,
                autoWidth: false,
                scrollX: true,
                language: {
                    search: "ค้นหา:",
                    lengthMenu: "แสดง _MENU_ รายการต่อหน้า",
                    info: "แสดง _START_ ถึง _END_ จากทั้งหมด _TOTAL_ รายการ",
                    infoEmpty: "ไม่มีข้อมูลให้แสดง",
                    infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
                    zeroRecords: "ไม่พบข้อมูลที่ค้นหา",
                    paginate: {
                        first: "หน้าแรก",
                        last: "หน้าสุดท้าย",
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    }
                }
            });

            $('table[id^="datatable"], table.data-table-auto').each(function() {
                const $table = $(this);
                if ($.fn.dataTable.isDataTable(this)) {
                    $table.DataTable().destroy();
                }
                $table.DataTable();
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="{{ asset('backend/assets/js/code.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js">
    </script>

    <script>
        $(function() {
            $('.datepicker-th').datepicker({
                format: 'dd/mm/yyyy',
                language: 'th',
                thaiyear: true,
                autoclose: true,
                todayHighlight: true
            }).on('show', function() {
                setTimeout(function() {
                    $('.datepicker-switch').each(function() {
                        const text = $(this).text();
                        const match = text.match(/(\d{4})$/);
                        if (match) {
                            const year = parseInt(match[1], 10);
                            if (year < 2500) {
                                $(this).text(text.replace(year, year + 543));
                            }
                        }
                    });

                    $('.datepicker-years .year').each(function() {
                        const year = parseInt($(this).text(), 10);
                        if (year < 2500) {
                            $(this).text(year + 543);
                        }
                    });
                }, 10);
            });
        });
    </script>

    <script>
        window.confirmDelete = function(formId, message = 'คุณต้องการลบข้อมูลนี้ใช่หรือไม่') {
            Swal.fire({
                title: 'ท่านแน่ใจ ?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ตกลง',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        };
    </script>

    <script>
        @if (Session::has('message'))
            var type = "{{ Session::get('alert-type', 'info') }}";
            switch (type) {
                case 'info':
                    toastr.info("{{ Session::get('message') }}");
                    break;
                case 'success':
                    toastr.success("{{ Session::get('message') }}");
                    break;
                case 'warning':
                    toastr.warning("{{ Session::get('message') }}");
                    break;
                case 'error':
                    toastr.error("{{ Session::get('message') }}");
                    break;
            }
        @endif
    </script>

    <script>
        $(function() {
            if (window.feather) {
                feather.replace();
            }
        });
    </script>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const body = document.body;

            const sidebarToggle =
                document.getElementById('clientSidebarToggle') ||
                document.querySelector('.button-toggle-menu');

            const sidebar = document.getElementById('clientSidebarPanel');
            const overlay = document.querySelector('.sidebar-overlay');

            const topbar =
                document.getElementById('appTopbar') ||
                document.querySelector('.app-topbar');

            const navbarCollapse =
                document.getElementById('navbarSupportedContent');

            if (!sidebarToggle || !sidebar) {
                return;
            }

            function isDesktop() {
                return window.matchMedia('(min-width: 1200px)').matches;
            }

            function syncTopbarHeight() {
                if (!topbar) {
                    return;
                }

                const height = Math.ceil(
                    topbar.getBoundingClientRect().height || 72
                );

                document.documentElement.style.setProperty(
                    '--topbar-height',
                    height + 'px'
                );

                /*
                 * ไม่กำหนด body.style.paddingTop แบบ inline
                 * เพราะ inline style จะทับ CSS และทำให้เกิดช่องว่างซ้ำ
                 */
                body.style.removeProperty('padding-top');
            }

            function sidebarIsVisible() {
                if (isDesktop()) {
                    return !body.classList.contains('client-sidebar-collapsed');
                }

                return body.classList.contains('sidebar-open');
            }

            function syncToggleState() {
                const visible = sidebarIsVisible();

                sidebarToggle.setAttribute(
                    'aria-expanded',
                    visible ? 'true' : 'false'
                );

                sidebarToggle.setAttribute(
                    'title',
                    visible
                        ? 'ปิดเมนูด้านข้าง'
                        : 'เปิดเมนูด้านข้าง'
                );
            }

            function refreshPageLayout() {
                window.setTimeout(function () {
                    window.dispatchEvent(new Event('resize'));

                    if (
                        window.jQuery &&
                        $.fn.dataTable &&
                        typeof $.fn.dataTable.tables === 'function'
                    ) {
                        $.fn.dataTable
                            .tables({
                                visible: true,
                                api: true
                            })
                            .columns.adjust();
                    }
                }, 260);
            }

            function closeMobileSidebar() {
                body.classList.remove('sidebar-open');
                syncToggleState();
            }

            function toggleSidebar(event) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();

                if (isDesktop()) {
                    body.classList.remove('sidebar-open');
                    body.classList.toggle('client-sidebar-collapsed');
                } else {
                    body.classList.remove('client-sidebar-collapsed');
                    body.classList.toggle('sidebar-open');
                }

                syncToggleState();
                refreshPageLayout();
            }

            sidebarToggle.addEventListener(
                'click',
                toggleSidebar,
                true
            );

            if (overlay) {
                overlay.addEventListener(
                    'click',
                    closeMobileSidebar
                );
            }

            document.addEventListener('keydown', function (event) {
                if (
                    event.key === 'Escape' &&
                    !isDesktop() &&
                    body.classList.contains('sidebar-open')
                ) {
                    closeMobileSidebar();
                }
            });

            window.addEventListener('resize', function () {
                syncTopbarHeight();

                if (isDesktop()) {
                    body.classList.remove('sidebar-open');
                } else {
                    body.classList.remove('client-sidebar-collapsed');
                }

                syncToggleState();
            });

            if (navbarCollapse) {
                navbarCollapse.addEventListener(
                    'shown.bs.collapse',
                    syncTopbarHeight
                );

                navbarCollapse.addEventListener(
                    'hidden.bs.collapse',
                    syncTopbarHeight
                );
            }

            if ('ResizeObserver' in window && topbar) {
                const topbarObserver = new ResizeObserver(function () {
                    syncTopbarHeight();
                });

                topbarObserver.observe(topbar);
            }

            syncTopbarHeight();
            syncToggleState();
        });
    </script>

    {{-- FORM_PERMISSION_UI_V7: centralized, global read-only protection --}}
    @include('components.form_permission_ui')

</body>

</html