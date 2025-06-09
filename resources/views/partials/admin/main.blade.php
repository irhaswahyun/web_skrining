<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ asset('favicon.ico') }}"> {{-- Pastikan favicon.ico ada di public/ --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Web Skrining Puskesmas Mojopanggung</title>
    <link rel="stylesheet" href="{{ asset('assets/css/simplebar.css') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dropzone.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/uppy.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.steps.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.timepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/quill.snow.css') }}">
    {{-- MENGHAPUS daterangepicker.css karena Anda tidak menginginkannya dan bisa konflik --}}
    {{-- <link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('assets/css/app-light.css') }}" id="lightTheme">
    <link rel="stylesheet" href="{{ asset('assets/css/app-dark.css') }}" id="darkTheme" disabled>

    {{-- **PENTING: Pastikan CSS jQuery UI dimuat di sini untuk styling kalender** --}}
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>

<body class="vertical light">
    <div class="wrapper">
        @include('partials.admin.navbar')
        @include('partials.admin.sidebar')
        <main role="main" class="main-content">
            @yield('content')
        </main>
    </div>

    {{-- ================================================================================= --}}
    {{-- START JAVASCRIPT SECTION (URUTANNYA SANGAT PENTING!)                                --}}
    {{-- ================================================================================= --}}

    {{-- 1. jQuery harus dimuat PERTAMA KALI (ini adalah inti dari semua yang lain) --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    {{-- 2. jQuery UI harus dimuat KEDUA (karena bergantung pada jQuery) --}}
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    {{-- 3. JQUERY UI I18N (untuk Bahasa Indonesia) harus dimuat KETIGA (karena bergantung pada jQuery UI) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/i18n/jquery.ui.datepicker-id.min.js"></script>

    {{-- 4. Kemudian, library lain yang bergantung pada jQuery atau jQuery UI bisa dimuat setelahnya --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/moment.min.js') }}"></script> {{-- Moment.js masih berguna untuk hal lain --}}
    <script src="{{ asset('assets/js/simplebar.min.js') }}"></script>
    {{-- MENGHAPUS daterangepicker.js karena Anda tidak menginginkannya dan bisa konflik --}}
    {{-- <script src='{{ asset('assets/js/daterangepicker.js') }}'></script> --}}
    <script src='{{ asset('assets/js/jquery.stickOnScroll.js') }}'></script>
    <script src="{{ asset('assets/js/tinycolor-min.js') }}"></script>
    <script src='{{ asset('assets/js/select2.min.js') }}'></script>
    <script src="{{ asset('assets/js/d3.min.js') }}"></script>
    <script src="{{ asset('assets/js/topojson.min.js') }}"></script>
    <script src="{{ asset('assets/js/datamaps.all.min.js') }}"></script>
    <script src="{{ asset('assets/js/datamaps-zoomto.js') }}"></script>
    <script src="{{ asset('assets/js/datamaps.custom.js') }}"></script>
    <script src="{{ asset('assets/js/gauge.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.sparkline.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.mask.min.js') }}"></script>
    <script src='{{ asset('assets/js/jquery.steps.min.js') }}'></script>
    <script src='{{ asset('assets/js/jquery.validate.min.js') }}'></script>
    <script src='{{ asset('assets/js/jquery.timepicker.js') }}'></script>
    <script src='{{ asset('assets/js/dropzone.min.js') }}'></script>
    <script src='{{ asset('assets/js/uppy.min.js') }}'></script>
    <script src='{{ asset('assets/js/quill.min.js') }}'></script>
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <script src="{{ asset('assets/js/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/js/apexcharts.custom.js') }}"></script>

    <script>
        // Set default bahasa untuk datepicker ke Bahasa Indonesia
        // Ini harus dilakukan setelah file lokalisasi jQuery UI dimuat (yaitu jquery.ui.datepicker-id.min.js)
        // Dan juga setelah DOM siap atau di dalam $(document).ready()
        $(document).ready(function() {
            $.datepicker.setDefaults($.datepicker.regional['id']);

            // Inisialisasi Select2 umum (jika ada)
            $('#pertanyaan_ids').select2(); // Jika ada select2 dengan ID ini
            $('.select2').select2({ theme: 'bootstrap4' });
            $('.select2-multi').select2({ multiple: true, theme: 'bootstrap4' });
        });

        Chart.defaults.global.defaultFontFamily = base.defaultFontFamily;
        Chart.defaults.global.defaultFontColor = colors.mutedColor;

        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'OK',
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'OK',
                });
            @endif
            feather.replace();
        });

        // Google Analytics
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'UA-56159088-1');
    </script>

    {{-- Ini adalah tempat @yield('scripts') Anda, tempat script dari index.blade.php akan dimuat. --}}
    @yield('scripts')

</body>

</html>
