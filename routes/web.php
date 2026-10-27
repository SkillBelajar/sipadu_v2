<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/dashboard', function () {
    return 'admin dashboard';
})->middleware(['auth', 'role:admin']);

Route::get('/admin/layanan', [\App\Http\Controllers\LayananController::class, 'index'])
    ->middleware(['auth', 'role:admin']);

Route::post('/admin/layanan', [\App\Http\Controllers\LayananController::class, 'store'])
    ->middleware(['auth', 'role:admin']);

Route::post('/sekolah/pengajuan', [\App\Http\Controllers\PengajuanController::class, 'store'])
    ->middleware(['auth', 'role:sekolah']);

Route::patch('/verifikator/dokumen/{dokumen_pengajuan}', [\App\Http\Controllers\VerifikasiController::class, 'update'])
    ->middleware(['auth', 'role:verifikator']);

Route::get('/sekolah/sarpras', [\App\Http\Controllers\LaporanSarprasController::class, 'index'])
    ->middleware(['auth', 'role:sekolah']);

Route::post('/sekolah/sarpras', [\App\Http\Controllers\LaporanSarprasController::class, 'store'])
    ->middleware(['auth', 'role:sekolah']);

Route::get('/sekolah/dashboard', function () {
    return 'sekolah dashboard';
})->middleware(['auth', 'role:sekolah']);

Route::get('/verifikator/dashboard', function () {
    return 'verifikator dashboard';
})->middleware(['auth', 'role:verifikator']);

Route::get('/kabid/dashboard', function () {
    return 'kabid dashboard';
})->middleware(['auth', 'role:kabid']);

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
