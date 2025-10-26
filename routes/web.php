<?php
// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PresensiSekolahController;
use App\Http\Controllers\PresensiSholatController;
use App\Http\Controllers\PresensiKustomController;
use App\Http\Controllers\KantinController;
use App\Http\Controllers\JadwalSholatController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PengaturanWaktuController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Presensi Sekolah
    Route::prefix('presensi-sekolah')->name('presensi.sekolah.')->group(function () {
        Route::get('/', [PresensiSekolahController::class, 'index'])->name('index');
        Route::post('/scan', [PresensiSekolahController::class, 'scan'])->name('scan');
        Route::post('/update-keterangan', [PresensiSekolahController::class, 'updateKeterangan'])->name('update');
    });
    
    // Presensi Sholat
    Route::prefix('presensi-sholat')->name('presensi.sholat.')->group(function () {
        Route::get('/', [PresensiSholatController::class, 'index'])->name('index');
        Route::post('/scan', [PresensiSholatController::class, 'scan'])->name('scan');
        Route::post('/update-keterangan', [PresensiSholatController::class, 'updateKeterangan'])->name('update');
        Route::get('/jadwal', [PresensiSholatController::class, 'getJadwal'])->name('jadwal');
    });
    
    // Presensi Kustom
    Route::prefix('presensi-kustom')->name('presensi.kustom.')->group(function () {
        Route::get('/', [PresensiKustomController::class, 'index'])->name('index');
        Route::post('/scan', [PresensiKustomController::class, 'scan'])->name('scan');
        Route::post('/update-keterangan', [PresensiKustomController::class, 'updateKeterangan'])->name('update');
        
        // Kelola Jadwal (Admin)
        Route::middleware(['admin'])->group(function () {
            Route::get('/jadwal', [PresensiKustomController::class, 'jadwalIndex'])->name('jadwal.index');
            Route::post('/jadwal', [PresensiKustomController::class, 'storeJadwal'])->name('jadwal.store');
            Route::put('/jadwal/{id}', [PresensiKustomController::class, 'updateJadwal'])->name('jadwal.update');
            Route::delete('/jadwal/{id}', [PresensiKustomController::class, 'destroyJadwal'])->name('jadwal.destroy');
        });
    });
    
    // E-Kantin
    Route::prefix('kantin')->name('kantin.')->group(function () {
        Route::get('/cek-saldo', [KantinController::class, 'cekSaldo'])->name('cek-saldo');
        Route::post('/scan-saldo', [KantinController::class, 'scanSaldo'])->name('scan-saldo');
        Route::get('/topup', [KantinController::class, 'topup'])->name('topup');
        Route::post('/proses-topup', [KantinController::class, 'prosesTopup'])->name('proses-topup');
        Route::get('/bayar', [KantinController::class, 'bayar'])->name('bayar');
        Route::post('/proses-bayar', [KantinController::class, 'prosesBayar'])->name('proses-bayar');
        Route::get('/riwayat', [KantinController::class, 'riwayat'])->name('riwayat');
        Route::post('/toggle-limit', [KantinController::class, 'toggleLimit'])->name('toggle-limit');
    });
    
    // User Management (Admin)
    Route::middleware(['admin'])->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });
    
    // Jadwal Sholat Sync (Admin)
    Route::middleware(['admin'])->prefix('jadwal-sholat')->name('jadwal-sholat.')->group(function () {
        Route::get('/', [JadwalSholatController::class, 'index'])->name('index');
        Route::post('/sync', [JadwalSholatController::class, 'syncJadwal'])->name('sync');
    });

    // Pengaturan Waktu (Admin)
    Route::middleware(['admin'])->prefix('pengaturan-waktu')->name('pengaturan.waktu.')->group(function () {
        Route::get('/', [PengaturanWaktuController::class, 'index'])->name('index');
        Route::post('/', [PengaturanWaktuController::class, 'create'])->name('create');
        Route::put('/{pengaturan}', [PengaturanWaktuController::class, 'update'])->name('update');
        Route::delete('/{pengaturan}', [PengaturanWaktuController::class, 'destroy'])->name('destroy');
    });
});