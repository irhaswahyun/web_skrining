<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Web Skrining Puskesmas Mojopanggung') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/app-light.css') }}">

    <!-- Scripts -->
    {{-- @vite(['resources/js/app.js']) --}}
</head>
<body class="font-sans antialiased">

    <div class="login-wrapper">
        <div class="login-box">
            <img src="{{ asset('assets/images/Iconlogin.png') }}" alt="Login Icon">
            <h5 class="mb-4 font-semibold text-gray-700">Login ke Sistem Skrining</h5>
            {{ $slot }}
        </div>
    </div>

    <!-- Aksen garis hijau -->
    <div class="green-line-left"></div>
    <div class="green-line-right"></div>

</body>
</html>
