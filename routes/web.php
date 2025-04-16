<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SkriningController;
use App\Http\Controllers\PasienController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('admin.adminDashboard');
})->middleware(['auth', 'verified'])->name('admin.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

//SKRINING
Route::resource('skrining', SkriningController::class);
Route::get('/skrining/create', [SkriningController::class, 'create'])->name('skrining.create');
Route::post('/skrining/store', [SkriningController::class, 'store'])->name('skrining.store');
//PASIEN
// Route::resource('pasien', PasienController::class);
Route::get('/manajemen-pasien', [PasienController::class, 'index'])->name('manajemen_pasien.index');
Route::get('/manajemen-pasien/create', [PasienController::class, 'create'])->name('manajemen_pasien.create');
Route::post('/manajemen-pasien/store', [PasienController::class, 'store'])->name('manajemen_pasien.store');
// Kalau kamu nanti mau edit, bisa tambahkan juga:
Route::get('/manajemen-pasien/{id}/edit', [PasienController::class, 'edit'])->name('manajemen_pasien.edit');