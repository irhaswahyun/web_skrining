<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SkriningController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\Daftar_PenyakitController;
use App\Http\Controllers\Daftar_PertanyaanController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\RiwayatSkriningController;
use App\Http\Controllers\RekapSkriningController;
use App\Http\Controllers\PasienSkriningController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

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


// ROLE ROUTES
Route::prefix('role')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('role.index');
    Route::post('/store', [RoleController::class, 'store'])->name('role.store');
    Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('role.edit');
    Route::put('/update/{id}', [RoleController::class, 'update'])->name('role.update');
    Route::get('/delete/{id}', [RoleController::class, 'delete'])->name('role.delete');
});

// PENGGUNA ROUTES
Route::prefix('pengguna')->group(function () {
    Route::get('/', [PenggunaController::class, 'index'])->name('pengguna.index');
    Route::post('/store', [PenggunaController::class, 'store'])->name('pengguna.store');
    Route::get('/edit/{id}', [PenggunaController::class, 'edit'])->name('pengguna.edit');
    Route::put('/update/{id}', [PenggunaController::class, 'update'])->name('pengguna.update');
    Route::get('/delete/{id}', [PenggunaController::class, 'delete'])->name('pengguna.delete');
});

// PASIEN ROUTES
Route::prefix('pasien')->group(function () {
    Route::get('/', [PasienController::class, 'index'])->name('pasien.index');
    Route::post('/store', [PasienController::class, 'store'])->name('pasien.store');
    Route::get('/edit/{id}', [PasienController::class, 'edit'])->name('pasien.edit');
    Route::put('/update/{id}', [PasienController::class, 'update'])->name('pasien.update');
    Route::get('/delete/{id}', [PasienController::class, 'delete'])->name('pasien.delete');
    Route::get('/get-data', [PasienController::class, 'getPasienData'])->name('pasien.getPasienData');
});

// DAFTAR PENYAKIT ROUTES
Route::prefix('daftar-penyakit')->group(function () {
    Route::get('/', [Daftar_PenyakitController::class, 'index'])->name('daftar_penyakit.index');
    Route::post('/store', [Daftar_PenyakitController::class, 'store'])->name('daftar_penyakit.store');
    Route::get('/edit/{id}', [Daftar_PenyakitController::class, 'edit'])->name('daftar_penyakit.edit');
    Route::put('/update/{id}', [Daftar_PenyakitController::class, 'update'])->name('daftar_penyakit.update');
    Route::get('/delete/{id}', [Daftar_PenyakitController::class, 'delete'])->name('daftar_penyakit.delete');
});

// DAFTAR PERTANYAAN ROUTES
Route::prefix('daftar-pertanyaan')->group(function () {
    Route::get('/', [Daftar_PertanyaanController::class, 'index'])->name('daftar_pertanyaan.index');
    Route::post('/store', [Daftar_PertanyaanController::class, 'store'])->name('daftar_pertanyaan.store');
    Route::get('/edit/{id}', [Daftar_PertanyaanController::class, 'edit'])->name('daftar_pertanyaan.edit');
    Route::put('/update/{id}', [Daftar_PertanyaanController::class, 'update'])->name('daftar_pertanyaan.update');
    Route::get('/delete/{id}', [Daftar_PertanyaanController::class, 'delete'])->name('daftar_pertanyaan.delete');
});

// FORM SKRINING ROUTES
Route::prefix('form-skrining')->group(function () {
    Route::get('/', [FormController::class, 'index'])->name('form_skrining.index');
    Route::post('/store', [FormController::class, 'store'])->name('form_skrining.store');
    Route::get('/detail/{id}', [FormController::class, 'detail'])->name('form_skrining.detail');
    Route::get('/edit/{id}', [FormController::class, 'edit'])->name('form_skrining.edit');
    Route::put('/update/{id}', [FormController::class, 'update'])->name('form_skrining.update');
    Route::delete('/delete/{formSkrining}', [FormController::class, 'delete'])->name('form_skrining.delete');
});

// SKRINING ROUTES
Route::prefix('skrining')->group(function () {
    Route::get('/', [SkriningController::class, 'index'])->name('skrining.index');
    Route::post('/store', [SkriningController::class, 'store'])->name('skrining.store');
    Route::put('/update/{id}', [SkriningController::class, 'update'])->name('skrining.update');
    Route::delete('/{id}', [SkriningController::class, 'delete'])->name('skrining.delete');
    Route::get('/show/{id}', [SkriningController::class, 'show'])->name('skrining.show');
    Route::get('/get-pertanyaan-by-form-skrining/{id}', [SkriningController::class, 'getPertanyaanByFormSkrining'])->name('skrining.get-pertanyaan-by-form-skrining');
    // Route::get('/get-form-skrinings-by-kategori', [SkriningController::class, 'getFormSkriningsByKategori'])->name('skrining.get-form-skrinings-by-kategori'); // Rute ini dihapus
});

// RIWAYAT SKRINING ROUTES
Route::prefix('riwayat-skrining')->group(function () {
    Route::get('/', [RiwayatSkriningController::class, 'index'])->name('riwayat_skrining.index');
    Route::get('/get-history', [RiwayatSkriningController::class, 'getHistory'])->name('riwayat_skrining.get_history');
});

// // REKAP HASIL SKRINING ROUTES
// Route::prefix('rekap-hasil-skrining')->group(function () {
//     // Route::get('/', [RekapSkriningController::class, 'index'])->name('rekap_hasil_skrining.index');
//     // Route::get('/summary', [RekapSkriningController::class, 'getRekapSummary'])->name('rekap_hasil_skrining.summary');
//     // Route::get('/pasien', [RekapSkriningController::class, 'getPasienByPenyakit'])->name('rekap_hasil_skrining.pasien');
//     // Route::get('/detail', [RekapSkriningController::class, 'getDetailSkrining'])->name('rekap_hasil_skrining.detail');
//     // Route::get('/pasien-list', [PasienSkriningController::class, 'index'])->name('rekap_hasil_skrining.pasien_list');
//     // Rute detail skrining tetap di RekapSkriningController karena itu adalah AJAX call untuk detail satu record
//     // Route::get('/detail', [RekapSkriningController::class, 'getDetailSkrining'])->name('rekap_hasil_skrining.detail');
//         Route::get('/', [RekapSkriningController::class, 'index'])->name('rekap_hasil_skrining.index');
//         Route::get('/summary', [RekapSkriningController::class, 'getSummary'])->name('rekap_hasil_skrining.summary');
//         // Route::get('/pasien-list', [RekapSkriningController::class, 'pasienList'])->name('rekap_hasil_skrining.pasien_list');
//         Route::get('/pasien-list', [PasienSkriningController::class, 'index'])->name('rekap_hasil_skrining.pasien_list');
//         Route::get('/detail', [RekapSkriningController::class, 'getDetailSkrining'])->name('rekap_hasil_skrining.detail'); // Rute untuk detail skrining
// });

// Pastikan rute rekap skrining ini ada dan sesuai
Route::prefix('rekap-hasil-skrining')->name('rekap_hasil_skrining.')->group(function () {
    Route::get('/', [RekapSkriningController::class, 'index'])->name('index');
    Route::get('/summary', [RekapSkriningController::class, 'getRekapSummary'])->name('summary');
    Route::get('/detail', [RekapSkriningController::class, 'getDetailSkrining'])->name('detail');

    // Rute untuk menampilkan daftar pasien skrining
    Route::get('/pasien-list', [RekapSkriningController::class, 'pasienList'])->name('pasien_list');
});