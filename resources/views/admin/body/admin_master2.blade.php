<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Dashboard | Admin Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc." />
    <meta name="author" content="Zoyothemes" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon.ico') }}">

    <!-- Core CSS -->
    <link href="{{ asset('backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />
    <link href="{{ asset('backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">

    <!-- Datatables CSS -->
    <link href="{{ asset('backend/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('backend/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('backend/assets/libs/datatables.net-keytable-bs5/css/keyTable.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('backend/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('backend/assets/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css') }}" rel="stylesheet" />

    <!-- ✅ Datepicker CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">

    <!-- ✅ Google Fonts (Kanit) -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;500;600&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Kanit', sans-serif;
        font-size: 15px;
        line-height: 1.6;
        color: #212529;
    }
    h1, h2, h3, .card-header, .navbar-brand {
        font-weight: 300;
    }
    table td, table th {
        font-size: 14px;
        vertical-align: middle;
    }
    .btn {
        font-weight: 400;
        letter-spacing: 0.5px;
    }
    .form-control {
        color: #6c757d;
    }
    .form-control::placeholder {
        color: #adb5bd;
        opacity: 1;
    }

    /* ปรับตัวอักษรในตาราง DataTable ให้เล็กลง */
    table.dataTable td, 
    table.dataTable th {
        font-size: 12px;   /* ปรับขนาดตามที่ต้องการ เช่น 12px */
    }

    /* ปรับตัวอักษรในส่วน control เช่น search, pagination */
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_paginate {
        font-size: 12px;
    }

</style>

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

   <!-- Vendor JS -->
<script src="{{ asset('backend/assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/waypoints/lib/jquery.waypoints.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/jquery.counterup/jquery.counterup.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/feather-icons/feather.min.js') }}"></script>

<!-- ApexCharts (ใช้ไฟล์เดียว ไม่ซ้ำ) -->
<script src="{{ asset('backend/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<!-- App JS -->
<script src="{{ asset('backend/assets/js/app.js') }}"></script>

<!-- DataTables JS -->
<script src="{{ asset('backend/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('backend/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="{{ asset('backend/assets/js/code.js') }}"></script>

<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Datepicker JS + Thai locale -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js"></script>

<!-- ✅ Custom Script -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // ✅ Chart
    var options = {
        chart: { type: 'pie', height: 350 },
        series: [
            {{ $maleCount ?? 0 }},
            {{ $femaleCount ?? 0 }}
        ],
        labels: ['ชาย','หญิง'],
        colors: ['#28a745','#dc3545'],
        legend: { position: 'bottom' }
    };
    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();

    // ✅ DataTable ภาษาไทย
    $('#clientsTable').DataTable({
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

    // ✅ Datepicker Thai
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
                    const year = parseInt(match[1]);
                    if (year < 2500) {
                        $(this).text(text.replace(year, year + 543));
                    }
                }
            });
            $('.datepicker-years .year').each(function() {
                const year = parseInt($(this).text());
                if (year < 2500) {
                    $(this).text(year + 543);
                }
            });
        }, 10);
    });
});
</script>

<!-- ✅ Toastr Alert -->
<script>
@if(Session::has('message'))
    var type = "{{ Session::get('alert-type','info') }}";
    switch(type){
        case 'info': toastr.info("{{ Session::get('message') }}"); break;
        case 'success': toastr.success("{{ Session::get('message') }}"); break;
        case 'warning': toastr.warning("{{ Session::get('message') }}"); break;
        case 'error': toastr.error("{{ Session::get('message') }}"); break;
    }
@endif
</script>
 <!-- ✅ Custom Script -->
    @stack('scripts')

    {{-- FORM_PERMISSION_UI_V7: centralized, global read-only protection --}}
    @include('components.form_permission_ui')

</body>
</html>

