<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/admin/dashboard', function () {
    $links = '
        <h2>Admin Dashboard</h2>
        <ul>
            <li><a href="/admin/layanan">Manajemen Layanan & Syarat</a></li>
            <li><a href="/profile">Profile</a></li>
            <li><form id="logout" action="/logout" method="POST" style="display:none;">' . csrf_field() . '</form>
            <a href="#" onclick="document.getElementById(\'logout\').submit(); return false;">Logout</a></li>
        </ul>
    ';

    return $links;
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
    $links = '
        <h2>Sekolah Dashboard</h2>
        <ul>
            <li><a href="/sekolah/pengajuan">Ajukan Layanan (Upload Dokumen)</a></li>
            <li><a href="/sekolah/sarpras">Laporan Sarpras</a></li>
            <li><a href="/profile">Profile</a></li>
            <li><form id="logout" action="/logout" method="POST" style="display:none;">' . csrf_field() . '</form>
            <a href="#" onclick="document.getElementById(\'logout\').submit(); return false;">Logout</a></li>
        </ul>
    ';

    return $links;
})->middleware(['auth', 'role:sekolah']);

Route::get('/verifikator/dashboard', function () {
    $links = '
        <h2>Verifikator Dashboard</h2>
        <ul>
            <li><a href="/verifikator/dashboard">Daftar Dokumen untuk Diverifikasi</a></li>
            <li><a href="/profile">Profile</a></li>
            <li><form id="logout" action="/logout" method="POST" style="display:none;">' . csrf_field() . '</form>
            <a href="#" onclick="document.getElementById(\'logout\').submit(); return false;">Logout</a></li>
        </ul>
    ';

    return $links;
})->middleware(['auth', 'role:verifikator']);

Route::get('/kabid/dashboard', function () {
    $links = '
        <h2>Kabid Dashboard</h2>
        <ul>
            <li><a href="/kabid/pengajuan">Pantau Semua Pengajuan</a></li>
            <li><a href="/kabid/sarpras">Rekapitulasi & Verifikasi Sarpras</a></li>
            <li><a href="/profile">Profile</a></li>
            <li><form id="logout" action="/logout" method="POST" style="display:none;">' . csrf_field() . '</form>
            <a href="#" onclick="document.getElementById(\'logout\').submit(); return false;">Logout</a></li>
        </ul>
    ';

    return $links;
})->middleware(['auth', 'role:kabid']);

Route::get('/kabid/pengajuan', [\App\Http\Controllers\DashboardKabidController::class, 'pengajuan'])
    ->middleware(['auth', 'role:kabid']);

Route::get('/kabid/sarpras', [\App\Http\Controllers\DashboardKabidController::class, 'sarpras'])
    ->middleware(['auth', 'role:kabid']);

Route::patch('/kabid/sarpras/{laporan_sarpras}/verify', [\App\Http\Controllers\DashboardKabidController::class, 'verify'])
    ->middleware(['auth', 'role:kabid']);

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
