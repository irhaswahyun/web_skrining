<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SkriningController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\Daftar_PenyakitController;
use App\Http\Controllers\Daftar_PertanyaanController;   
use App\Http\Controllers\FormController;

Route::get('/', function () {
    return view('auth.login');
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


//ROLE
Route::prefix('role')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('role.index');
    // Route::get('/create', [RoleController::class, 'create'])->name('role.create');
    Route::post('/store', [RoleController::class, 'store'])->name('role.store');
    Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('role.edit');
    Route::put('/update/{id}', [RoleController::class, 'update'])->name('role.update'); // Ganti POST dengan PUT
    Route::get('/delete/{id}', [RoleController::class, 'delete'])->name('role.delete');
});

// PENGGUNA
Route::prefix('pengguna')->group(function () {
    Route::get('/', [PenggunaController::class, 'index'])->name('pengguna.index');
    // Route::get('/create', [PenggunaController::class, 'create'])->name('pengguna.create');
    Route::post('/store', [PenggunaController::class, 'store'])->name('pengguna.store');
    Route::get('/edit/{id}', [PenggunaController::class, 'edit'])->name('pengguna.edit');
    Route::put('/update/{id}', [PenggunaController::class, 'update'])->name('pengguna.update'); // Gunakan PUT untuk update
    Route::get('/delete/{id}', [PenggunaController::class, 'delete'])->name('pengguna.delete');
});

//PASIEN
Route::prefix('pasien')->group(function () {
    Route::get('/', [PasienController::class, 'index'])->name('pasien.index');
    // Route::get('/create', [PasienController::class, 'create'])->name('pasien.create');
    Route::post('/store', [PasienController::class, 'store'])->name('pasien.store');
    Route::get('/edit/{id}', [PasienController::class, 'edit'])->name('pasien.edit');
    Route::put('/update/{id}', [PasienController::class, 'update'])->name('pasien.update'); // Ganti POST dengan PUT
    Route::get('/delete/{id}', [PasienController::class, 'delete'])->name('pasien.delete');
});

// DAFTAR PENYAKIT
Route::prefix('daftar-penyakit')->group(function () {
    Route::get('/', [Daftar_PenyakitController::class, 'index'])->name('daftar_penyakit.index');
    // Route::get('/create', [Daftar_PenyakitController::class, 'create'])->name('daftar_penyakit.create');
    Route::post('/store', [Daftar_PenyakitController::class, 'store'])->name('daftar_penyakit.store');
    Route::get('/edit/{id}', [Daftar_PenyakitController::class, 'edit'])->name('daftar_penyakit.edit');
    Route::put('/update/{id}', [Daftar_PenyakitController::class, 'update'])->name('daftar_penyakit.update'); // Gunakan PUT untuk update
    Route::get('/delete/{id}', [Daftar_PenyakitController::class, 'delete'])->name('daftar_penyakit.delete');
});

// DAFTAR PERTANYAAN
Route::prefix('daftar-pertanyaan')->group(function () {
    Route::get('/', [Daftar_PertanyaanController::class, 'index'])->name('daftar_pertanyaan.index');
    // Route::get('/create', [Daftar_PenyakitController::class, 'create'])->name('daftar_penyakit.create');
    Route::post('/store', [Daftar_PertanyaanController::class, 'store'])->name('daftar_pertanyaan.store');
    Route::get('/edit/{id}', [Daftar_PertanyaanController::class, 'edit'])->name('daftar_pertanyaan.edit');
    Route::put('/update/{id}', [Daftar_PertanyaanController::class, 'update'])->name('daftar_pertanyaan.update'); // Gunakan PUT untuk update
    Route::get('/delete/{id}', [Daftar_PertanyaanController::class, 'delete'])->name('daftar_pertanyaan.delete');
});

// FORM SKRINING
Route::prefix('form-skrining')->group(function () {
    Route::get('/', [FormController::class, 'index'])->name('form_skrining.index');
    Route::post('/store', [FormController::class, 'store'])->name('form_skrining.store');
    Route::get('/detail/{id}', [FormController::class, 'detail'])->name('form_skrining.detail');
    Route::get('/edit/{id}', [FormController::class, 'edit'])->name('form_skrining.edit');
    Route::put('/update/{id}', [FormController::class, 'update'])->name('form_skrining.update');
    Route::delete('/delete/{id}', [FormController::class, 'delete'])->name('form_skrining.delete');
});

//SKRINING
// Route::resource('skrining', SkriningController::class);
// Route::get('/skrining/create', [SkriningController::class, 'create'])->name('skrining.create');
// Route::post('/skrining/store', [SkriningController::class, 'store'])->name('skrining.store');