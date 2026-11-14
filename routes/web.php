<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\TamuWebController;
use App\Http\Controllers\Web\PermohonanController;
use App\Http\Controllers\Web\PegawaiController;
use App\Http\Controllers\Web\LaporanWebController;
use App\Http\Controllers\Web\KunjunganController;
use App\Http\Controllers\Web\ScanController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Web\QrSitusController;
use App\Http\Controllers\Web\WalkinController;
use App\Http\Controllers\Web\PengaturanController;
use App\Http\Controllers\Web\NotifikasiController;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login',   [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login',  [AuthWebController::class, 'login'])->name('login.post');
Route::get('/daftar',  [AuthWebController::class, 'showRegister'])->name('register');
Route::post('/daftar', [AuthWebController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    // Tamu & pegawai
    Route::get('/kunjungan',         [KunjunganController::class, 'index'])->name('kunjungan.index');
    Route::get('/kunjungan/ajukan',  [KunjunganController::class, 'create'])->name('kunjungan.create');
    Route::post('/kunjungan',        [KunjunganController::class, 'store'])->name('kunjungan.store');
    Route::get('/kunjungan/{id}',    [KunjunganController::class, 'show'])->name('kunjungan.show');

    // Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/tamu',      [TamuWebController::class, 'index'])->name('tamu.index');
        // Route::get('/tamu/{id}', [TamuWebController::class, 'show'])->name('tamu.show');
        Route::get('/tamu/{sumber}/{id}', [TamuWebController::class, 'show'])
            ->whereIn('sumber', ['terjadwal', 'langsung'])
            ->name('tamu.show');

        Route::get('/permohonan',               [PermohonanController::class, 'index'])->name('permohonan.index');
        Route::get('/permohonan/{id}',          [PermohonanController::class, 'show'])->name('permohonan.show');
        Route::post('/permohonan/{id}/approve', [PermohonanController::class, 'approve'])->name('permohonan.approve');
        Route::post('/permohonan/{id}/reject',  [PermohonanController::class, 'reject'])->name('permohonan.reject');
        Route::post('/permohonan/{id}/complete',[PermohonanController::class, 'complete'])->name('permohonan.complete');

        Route::get('/scan',           [ScanController::class, 'index'])->name('scan.index');
        Route::post('/scan/lookup',   [ScanController::class, 'lookup'])->name('scan.lookup');
        Route::post('/scan/confirm',  [ScanController::class, 'confirm'])->name('scan.confirm');
        Route::post('/scan/complete', [ScanController::class, 'complete'])->name('scan.complete');

        Route::get('/pegawai',           [PegawaiController::class, 'index'])->name('pegawai.index');
        Route::get('/pegawai/tambah',    [PegawaiController::class, 'create'])->name('pegawai.create');
        Route::post('/pegawai',          [PegawaiController::class, 'store'])->name('pegawai.store');
        Route::get('/pegawai/{id}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
        Route::put('/pegawai/{id}',      [PegawaiController::class, 'update'])->name('pegawai.update');
        Route::delete('/pegawai/{id}',   [PegawaiController::class, 'destroy'])->name('pegawai.destroy');

        Route::get('/laporan',              [LaporanWebController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export-pdf',   [LaporanWebController::class, 'exportPdf'])->name('laporan.pdf');
        Route::get('/laporan/export-excel', [LaporanWebController::class, 'exportExcel'])->name('laporan.excel');

        Route::get('/qr-situs',        [QrSitusController::class, 'index'])->name('qr.index');
        Route::post('/qr-situs',       [QrSitusController::class, 'simpan'])->name('qr.simpan');
        Route::get('/qr-situs/unduh',  [QrSitusController::class, 'unduh'])->name('qr.unduh');
        Route::get('/qr-situs/poster', [QrSitusController::class, 'poster'])->name('qr.poster');

        Route::get('/langsung',               [WalkinController::class, 'index'])->name('walkin.index');
        Route::post('/langsung/{id}/masuk',   [WalkinController::class, 'masuk'])->name('walkin.masuk');
        Route::post('/langsung/{id}/selesai', [WalkinController::class, 'selesai'])->name('walkin.selesai');
        Route::delete('/langsung/{id}',       [WalkinController::class, 'destroy'])->name('walkin.destroy');

        Route::get('/pengaturan',  [PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::post('/pengaturan', [PengaturanController::class, 'simpan'])->name('pengaturan.simpan');

        Route::get('/notifikasi/tamu-baru', [NotifikasiController::class, 'tamuBaru'])->name('notif.tamu');

    });
});

Route::get('/mulai', function () {
    if (Auth::check()) {
        return redirect(Auth::user()->role === 'admin' ? route('dashboard') : route('kunjungan.index'));
    }
    return view('web.mulai');
})->name('mulai');

Route::get('/tamu-langsung',  [WalkinController::class, 'create'])->name('walkin.create');
Route::post('/tamu-langsung', [WalkinController::class, 'store'])->middleware('throttle:6,1')->name('walkin.store');
Route::get('/tamu-langsung/status/{token}', [WalkinController::class, 'status'])->name('walkin.status');

Route::get('/storage/{path}', function (string $path) {
    $base = realpath(storage_path('app/public'));
    $file = realpath($base.'/'.$path);

    abort_unless($file && str_starts_with($file, $base) && is_file($file), 404);

    $headers = ['Cache-Control' => 'public, max-age=604800'];

    if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'svg') {
        $headers['Content-Type'] = 'image/svg+xml';
    }

    return response()->file($file, $headers);
})->where('path', '.*');

Route::get('/tamu-terbaru', [NotifikasiController::class, 'publik'])
    ->middleware('throttle:30,1')
    ->name('notif.publik');