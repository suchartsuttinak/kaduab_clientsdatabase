<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ระบบจัดการข้อมูล')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ระบบจัดการข้อมูลผู้รับบริการ" />
    <meta name="author" content="Suchart" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon.ico') }}">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    {{-- Core CSS --}}
    <link href="{{ asset('backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />
    <link href="{{ asset('backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    {{-- Datatables --}}
    <link href="{{ asset('backend/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('backend/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('backend/assets/libs/datatables.net-keytable-bs5/css/keyTable.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('backend/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('backend/assets/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css') }}" rel="stylesheet" />

    {{-- Toastr --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">

    {{-- Datepicker --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">

    {{-- Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root{
            --adm-font: 'Kanit', sans-serif;
            --adm-bg: #f5f7fb;
            --adm-surface: #ffffff;
            --adm-text: #1f2937;
            --adm-muted: #6b7280;
            --adm-border: #e5e7eb;
            --adm-primary: #2563eb;
            --adm-primary-soft: #eff6ff;
            --adm-hover: #f8fafc;
            --adm-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            --adm-radius: 16px;
            --adm-radius-sm: 12px;
            --adm-sidebar-width: 280px;
        }

        html, body {
            font-family: var(--adm-font);
            background: var(--adm-bg);
            color: var(--adm-text);
            font-size: 14px;
            line-height: 1.6;
        }

        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        h1, h2, h3, h4, h5, h6 {
            color: var(--adm-text);
            font-weight: 600;
            letter-spacing: -.01em;
        }

        a {
            text-decoration: none;
        }

        .content-page {
            background: var(--adm-bg);
        }

        .card,
        .modal-content,
        .dropdown-menu {
            border: 1px solid rgba(229, 231, 235, .9);
            box-shadow: var(--adm-shadow);
            border-radius: var(--adm-radius);
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid var(--adm-border);
            border-top-left-radius: var(--adm-radius) !important;
            border-top-right-radius: var(--adm-radius) !important;
        }

        .form-control,
        .form-select {
            min-height: 44px;
            border-radius: 12px;
            border: 1px solid #d1d5db;
            color: var(--adm-text);
            box-shadow: none;
            transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
        }

        .form-control::placeholder {
            color: #9ca3af;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: rgba(37, 99, 235, .45);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .10);
        }

        .btn {
            min-height: 40px;
            border-radius: 12px;
            font-weight: 500;
            letter-spacing: .01em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
        }

        .btn-sm {
            min-height: 34px;
            border-radius: 10px;
        }

        table td,
        table th {
            vertical-align: middle;
        }

        table.dataTable td,
        table.dataTable th {
            font-size: 13px;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            font-size: 13px;
        }

        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border-radius: 10px;
            border: 1px solid #d1d5db;
            min-height: 36px;
            padding: .375rem .75rem;
        }

        .page-title-box h4,
        .page-title {
            font-weight: 600;
        }

        /* Sidebar */
        .admin-sidebar {
            background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
            border-right: 1px solid var(--adm-border);
        }

        .sidebar-brand {
            padding: 1rem 1rem .75rem;
            border-bottom: 1px solid var(--adm-border);
            margin-bottom: .5rem;
        }

        .sidebar-brand-link {
            display: flex;
            align-items: center;
            gap: .85rem;
            padding: .5rem;
            border-radius: 14px;
            transition: background-color .2s ease, transform .2s ease;
        }

        .sidebar-brand-link:hover {
            background: #f8fafc;
        }

        .sidebar-brand-logo {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--adm-primary-soft);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .sidebar-brand-logo img {
            max-width: 24px;
            max-height: 24px;
            object-fit: contain;
        }

        .sidebar-brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
            color: var(--adm-text);
        }

        .sidebar-brand-text strong {
            font-size: 15px;
            font-weight: 600;
        }

        .sidebar-brand-text small {
            font-size: 12px;
            color: var(--adm-muted);
        }

        .side-menu-list {
            padding: .75rem .75rem 1rem;
        }

        #side-menu .menu-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #94a3b8;
            padding: .9rem .85rem .5rem;
            margin-top: .25rem;
        }

        .sidebar-item {
            margin-bottom: .35rem;
        }

        .sidebar-link {
            min-height: 46px;
            display: flex !important;
            align-items: center;
            gap: .8rem;
            padding: .72rem .9rem;
            border-radius: 14px;
            color: #334155 !important;
            transition: all .2s ease;
            position: relative;
        }

        .sidebar-link:hover {
            background: var(--adm-hover);
            color: #0f172a !important;
        }

        .sidebar-item.is-open > .sidebar-link {
            background: #f8fafc;
        }

        .sidebar-link-icon {
            width: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-link-text {
            flex: 1 1 auto;
            font-weight: 500;
        }

        .nav-second-level {
            padding: .35rem 0 .2rem 0;
            margin: 0;
            list-style: none;
        }

        .nav-second-level li {
            margin: .2rem 0;
        }

        .nav-second-level .tp-link {
            display: flex;
            align-items: center;
            min-height: 40px;
            padding: .56rem .9rem .56rem 3.05rem;
            color: #64748b !important;
            border-radius: 12px;
            font-size: 13px;
            transition: all .2s ease;
            position: relative;
        }

        .nav-second-level .tp-link::before {
            content: "";
            position: absolute;
            left: 1.9rem;
            top: 50%;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #cbd5e1;
            transform: translateY(-50%);
            transition: all .2s ease;
        }

        .nav-second-level .tp-link:hover {
            background: #f8fafc;
            color: #0f172a !important;
        }

        .nav-second-level .tp-link.active {
            background: var(--adm-primary-soft);
            color: var(--adm-primary) !important;
            font-weight: 600;
        }

        .nav-second-level .tp-link.active::before {
            background: var(--adm-primary);
        }

        .menu-arrow {
            transition: transform .2s ease;
        }

        .sidebar-link[aria-expanded="true"] .menu-arrow {
            transform: rotate(90deg);
        }

        /* Scrollbar */
        [data-simplebar] .simplebar-scrollbar:before {
            background-color: rgba(100, 116, 139, .35);
        }

        /* Mobile */
        @media (max-width: 991.98px) {
            html, body {
                font-size: 13.5px;
            }

            .content-page {
                padding-bottom: 1rem;
            }

            .sidebar-brand {
                padding-top: .85rem;
            }

            .sidebar-link,
            .nav-second-level .tp-link,
            .btn,
            .form-control,
            .form-select {
                min-height: 44px;
            }
        }
    </style>

    @stack('styles')
</head>

<body data-menu-color="light" data-sidebar="default">
    <div id="app-layout">
        @include('admin.body.header')
        @include('admin.body.sidebar')

        <div class="content-page">
            @yield('admin')
            @include('admin.body.footer')
        </div>
    </div>

    {{-- Vendor JS --}}
    <script src="{{ asset('backend/assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/waypoints/lib/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/jquery.counterup/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/app.js') }}"></script>

    {{-- DataTables --}}
    <script src="{{ asset('backend/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>

    {{-- Alert / Notify --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="{{ asset('backend/assets/js/code.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    {{-- Datepicker --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Feather icons
            if (window.feather) {
                feather.replace();
            }

            // Datepicker TH (ใช้เฉพาะ element ที่มี class .datepicker-th)
            if (typeof $ !== 'undefined' && $.fn.datepicker) {
                $('.datepicker-th').datepicker({
                    format: 'dd/mm/yyyy',
                    language: 'th',
                    thaiyear: true,
                    autoclose: true,
                    todayHighlight: true
                }).on('show', function () {
                    setTimeout(function () {
                        $('.datepicker-switch').each(function () {
                            const text = $(this).text();
                            const match = text.match(/(\d{4})$/);
                            if (match) {
                                const year = parseInt(match[1], 10);
                                if (year < 2500) {
                                    $(this).text(text.replace(year, year + 543));
                                }
                            }
                        });

                        $('.datepicker-years .year').each(function () {
                            const year = parseInt($(this).text(), 10);
                            if (year < 2500) {
                                $(this).text(year + 543);
                            }
                        });
                    }, 10);
                });
            }

            // DataTable แบบกลาง เรียกใช้โดยใส่ class .datatable-th
            if (typeof $ !== 'undefined' && $.fn.DataTable) {
                $('.datatable-th').each(function () {
                    if (!$.fn.DataTable.isDataTable(this)) {
                        $(this).DataTable({
                            responsive: true,
                            autoWidth: false,
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
                    }
                });
            }
        });
    </script>

    {{-- Toastr --}}
    <script>
        @if(Session::has('message'))
            (function () {
                var type = "{{ Session::get('alert-type', 'info') }}";
                var message = @json(Session::get('message'));

                switch(type){
                    case 'success': toastr.success(message); break;
                    case 'warning': toastr.warning(message); break;
                    case 'error': toastr.error(message); break;
                    default: toastr.info(message); break;
                }
            })();
        @endif
    </script>

    @stack('scripts')
    {{-- FORM_PERMISSION_UI_V7: centralized, global read-only protection --}}
    @include('components.form_permission_ui')

</body>
</html>