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
    <link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app-light.css') }}" id="lightTheme">
    <link rel="stylesheet" href="{{ asset('assets/css/app-dark.css') }}" id="darkTheme" disabled>
    {{-- Baris ini dikomentari karena Anda menyatakan tidak ada file admin.css --}}
    {{-- <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}"> --}}
    {{-- Pastikan ini adalah versi select2 yang ingin Anda gunakan. Jika ada versi lokal di assets/css/select2.css, pertimbangkan untuk menghapus CDN ini atau sebaliknya --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" 
    integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" 
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    

    {{-- ================================================================================= --}}
    {{-- END CSS SECTION                                                                   --}}
    {{-- ================================================================================= --}}
</head>

<body class="vertical light">
    <div class="wrapper">
        @include('partials.admin.navbar')

        {{-- Ini adalah lokasi placeholder untuk tombol/switch mode gelap/terang --}}
        {{-- Anda bisa memindahkannya ke dalam partials.admin.navbar atau partials.admin.sidebar --}}
        {{-- Sesuaikan kelas CSS-nya agar sesuai dengan tata letak Anda --}}
        {{-- <div style="position: absolute; top: 20px; right: 20px; z-index: 1000;">
            <button id="modeSwitcher" class="btn btn-sm btn-outline-secondary">
                <span class="fe fe-moon" style="font-size: 1.2em;"></span> </button>
        </div> --}}

        @include('partials.admin.sidebar')

        <main role="main" class="main-content">
            @yield('content')
        </main> {{-- Tutup main-content --}}

    </div> {{-- Tutup wrapper --}}

    {{-- ================================================================================= --}}
    {{-- START JAVASCRIPT SECTION                                                          --}}
    {{-- ================================================================================= --}}

   
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/simplebar.min.js') }}"></script>
    <script src='{{ asset('assets/js/daterangepicker.js') }}'></script>
    <script src='{{ asset('assets/js/jquery.stickOnScroll.js') }}'></script>
char
    {{-- Tinycolor HARUS dimuat sebelum config.js --}}
    <script src="{{ asset('assets/js/tinycolor-min.js') }}"></script>

    {{-- Select2 (pilih salah satu CDN atau lokal) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    {{-- <script src='{{ asset('assets/js/select2.min.js') }}'></script> --}} {{-- Duplikat, hapus --}}

    {{-- DataTables (Tambahkan di sini jika Anda menggunakannya) --}}
    {{-- <script src="{{ asset('path/to/jquery.dataTables.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('path/to/dataTables.bootstrap4.min.js') }}"></script> --}}

    {{-- Plugin-plugin lain --}}
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


    {{-- 4. SWEETALERT2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    {{-- 5. SCRIPT YANG MENDEFINISIKAN 'colors' dan 'base' (config.js) --}}
    {{-- Ini harus dimuat SETELAH tinycolor.js DAN SETELAH elemen HTML seperti #modeSwitcher, #darkTheme, #lightTheme ada di DOM. --}}
    <script src="{{ asset('assets/js/config.js') }}"></script>


    {{-- 6. LIBRARY CHARTING (Chart.js, ApexCharts) DAN SCRIPT KUSTOM YANG MENGGUNAKAN 'colors'/'base' --}}
    <script src="{{ asset('assets/js/Chart.min.js') }}"></script> {{-- Dimuat sebelum script yang menggunakan Chart --}}
    <script src="{{ asset('assets/js/apexcharts.min.js') }}"></script> {{-- Dimuat sebelum script yang menggunakan ApexCharts --}}
    <script src="{{ asset('assets/js/apexcharts.custom.js') }}"></script> {{-- Dimuat setelah apexcharts.min.js --}}

    {{-- Sekarang script ini seharusnya menemukan 'base' dan 'colors' karena config.js sudah dimuat --}}
    <script>
        Chart.defaults.global.defaultFontFamily = base.defaultFontFamily;
        Chart.defaults.global.defaultFontColor = colors.mutedColor;
    </script>


    {{-- 7. SCRIPT BAWAAN TEMPLATE / SCRIPT KUSTOM UTAMA LAINNYA --}}
    <script src="{{ asset('assets/js/apps.js') }}"></script> {{-- Ini adalah script utama template Anda --}}


    {{-- 8. SCRIPT CUSTOM ANDA (TERMASUK GOOGLE ANALYTICS DAN INISIALISASI SELECT2) --}}
    <script>
        // Jalankan kode jQuery setelah DOM siap
        $(document).ready(function() {
            // Inisialisasi Select2
            $('#pertanyaan_ids').select2(); // Aktifkan Select2 pada select dengan id pertanyaan_ids
            $('.select2').select2({
                theme: 'bootstrap4',
            });
            $('.select2-multi').select2({
                multiple: true,
                theme: 'bootstrap4',
            });

            // Tambahkan inisialisasi plugin lain yang ada di sini
            // Contoh:
            // $('.drgpicker').daterangepicker({
            //     singleDatePicker: true,
            //     timePicker: false,
            //     locale: { format: 'YYYY-MM-DD' }
            // });
            // $('#some-mask').mask('000-000-0000');
            // var editor = new Quill('#editor', {
            //     theme: 'snow'
            // });
        });

        // Jalankan kode setelah seluruh konten DOM dimuat
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
            feather.replace(); // Dijalankan setelah feather.min.js dimuat
        });

        // Google Analytics
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'UA-56159088-1');
    </script>


    {{-- Ini adalah tempat untuk script-script spesifik dari masing-masing view. Pastikan ini yang PALING AKHIR. --}}
    @yield('scripts')

    {{-- ================================================================================= --}}
    {{-- END JAVASCRIPT SECTION                                                            --}}
    {{-- ================================================================================= --}}
</body>

</html>