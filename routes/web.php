<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PerizinanController;

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



Route::middleware(['guest:karyawan'])->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/proseslogin', [AuthController::class, 'proseslogin']);
});

Route::middleware(['auth:karyawan'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/proseslogout', [AuthController::class, 'proseslogout']);

    Route::get('/presensi/create', [PresensiController::class, 'create']);
    Route::post('/presensi/store', [PresensiController::class, 'store']);

    Route::get('/editprofile', [ProfileController::class, 'editprofile']);
    Route::post('/{id_karyawan}/updateprofile', [ProfileController::class, 'updateprofile']);

    Route::get('/rekap', [RekapController::class, 'rekap']);
    Route::post('/getrekap', [RekapController::class, 'getrekap']);

    Route::get('/izin', [PerizinanController::class, 'izin']);
    Route::get('/buatizin', [PerizinanController::class, 'buatizin']);
    Route::post('/upload', [PerizinanController::class, 'upload']);
    Route::post('/presensi/cekperizinan', [PerizinanController::class, 'cekperizinan']);
});

Route::middleware(['guest:user'])->group(function () {
    Route::get('/admin', function () {
        return view('auth.loginadmin');
    })->name('loginadmin');

    Route::post('/prosesloginadmin', [AuthController::class, 'prosesloginadmin']);
});

Route::middleware(['auth:user'])->group(function () {
    Route::get('/admin/dashboardadmin', [DashboardController::class, 'dashboardadmin']);
    Route::get('/proseslogoutadmin', [AuthController::class, 'proseslogoutadmin']);

    Route::get('/karyawan', [KaryawanController::class, 'index']);
    Route::post('/karyawan/store', [KaryawanController::class, 'store']);
    Route::post('/karyawan/edit', [KaryawanController::class, 'edit']);
    Route::post('/karyawan/{id_karyawan}/update', [KaryawanController::class, 'update']);
    Route::delete('/karyawan/{id_karyawan}/delete', [KaryawanController::class, 'delete']);

    Route::post('/getpresensi', [PresensiController::class, 'getpresensi']);
    Route::get('/presensi/laporanrekap', [PresensiController::class, 'laporanrekap']);
    Route::get('/presensi/laporanrekap/cetak', [PresensiController::class, 'cetakPDF'])->name('presensi.laporanrekap.cetak');
    // Route::get('/presensi/laporan', [PresensiController::class, 'laporan']);
    // Route::post('/presensi/cetaklaporan', [PresensiController::class, 'cetaklaporan']);
    // Route::get('/presensi/rekap', [PresensiController::class, 'rekap']);
    // Route::post('/presensi/cetakrekap', [PresensiController::class, 'cetakrekap']);

    Route::get('/presensi/izinsakit', [PerizinanController::class, 'izinsakit']);
    Route::post('/presensi/approvedizinsakit', [PerizinanController::class, 'approvedizinsakit']);
    Route::get('/presensi/{id}/batalkanizinsakit', [PerizinanController::class, 'batalkanizinsakit']);

});


