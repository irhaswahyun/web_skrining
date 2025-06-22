<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Skrining Puskesmas Mojopanggung</title>
    <link rel="icon" href="{{ asset('assets/images/Iconlogin.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('assets/css/app-light.css') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="login-wrapper">

        <!-- Garis Hijau Kiri dan Kanan -->
        <div class="green-line-left"></div>
        <div class="green-line-right"></div>

        <!-- Box Login -->
        <form method="POST" action="{{ route('login') }}" class="login-box">
            @csrf

            <!-- Logo -->
            <img src="{{ asset('assets/images/Iconlogin.png') }}" alt="Logo Login">

            <!-- Judul -->
            <h5>Login ke Sistem Skrining</h5>

            <!-- Username -->
            <div>
                <label for="email">Username</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus>
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <!-- Password -->
            <div>
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <!-- Tombol -->
            <button type="submit" class="btn-gradient">MASUK</button>
        </form>
    </div>
</body>
</html>
