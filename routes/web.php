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
    // Presensi Sholat Routes (gunakan prefix presensi-sholat)
    Route::prefix('presensi-sholat')->name('presensi.sholat.')->group(function () {
        Route::get('/', [PresensiSholatController::class, 'index'])->name('index');
        Route::post('/scan', [PresensiSholatController::class, 'scan'])->name('scan');
        Route::post('/update-keterangan', [PresensiSholatController::class, 'updateKeterangan'])->name('update');
        Route::get('/jadwal', [PresensiSholatController::class, 'getJadwal'])->name('jadwal');
        Route::get('/latest', [PresensiSholatController::class, 'getLatestPresensi'])->name('latest');
        
        // Export (opsional)
        Route::get('/export', [PresensiSholatController::class, 'export'])->name('export');
    });
    
    // Presensi Kustom
            Route::prefix('presensi-kustom')->name('presensi.kustom.')->group(function () {
        Route::get('/', [PresensiKustomController::class, 'index'])->name('index');
        Route::post('/scan', [PresensiKustomController::class, 'scan'])->name('scan');
        Route::post('/update-keterangan', [PresensiKustomController::class, 'updateKeterangan'])->name('update');
        
        // AJAX Routes
        Route::get('/latest', [PresensiKustomController::class, 'getLatestPresensi'])->name('latest');
        Route::get('/stats', [PresensiKustomController::class, 'getStats'])->name('stats');
        Route::get('/belum-presensi/{jadwalId}', [PresensiKustomController::class, 'getBelumPresensi'])->name('belum.presensi'); // 🆕
        
        // Kelola Jadwal (Admin only)
        Route::middleware(['admin'])->prefix('jadwal')->name('jadwal.')->group(function () {
            Route::get('/', [PresensiKustomController::class, 'jadwalIndex'])->name('index');
            Route::post('/', [PresensiKustomController::class, 'storeJadwal'])->name('store');
            Route::put('/{id}', [PresensiKustomController::class, 'updateJadwal'])->name('update');
            Route::delete('/{id}', [PresensiKustomController::class, 'destroyJadwal'])->name('destroy');
        });
    });
    
// E-Kantin
Route::prefix('kantin')->name('kantin.')->group(function () {
    Route::get('/cek-saldo', [KantinController::class, 'cekSaldo'])->name('cek-saldo');
    Route::post('/scan-saldo', [KantinController::class, 'scanSaldo'])->name('scan-saldo');
    
    // ✅ TOPUP dengan SCAN RFID
    Route::get('/topup', [KantinController::class, 'topup'])->name('topup');
    Route::post('/cari-user-topup', [KantinController::class, 'cariUserTopup'])->name('cari-user-topup'); // 🆕 ROUTE BARU
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
    
    // Jadwal Sholat Management (Admin)
    Route::middleware(['admin'])->prefix('jadwal-sholat')->name('jadwal-sholat.')->group(function () {
    Route::get('/', [JadwalSholatController::class, 'index'])->name('index');
    Route::post('/sync', [JadwalSholatController::class, 'syncJadwal'])->name('sync');
    Route::post('/bulk-override', [JadwalSholatController::class, 'bulkOverride'])->name('bulk-override');
    Route::put('/{id}', [JadwalSholatController::class, 'update'])->name('update');
    Route::delete('/{id}', [JadwalSholatController::class, 'destroy'])->name('destroy');
});

    // Pengaturan Waktu (Admin)
    Route::middleware(['admin'])->prefix('pengaturan-waktu')->name('pengaturan.waktu.')->group(function () {
        Route::get('/', [PengaturanWaktuController::class, 'index'])->name('index');
        Route::post('/', [PengaturanWaktuController::class, 'create'])->name('create');
        Route::put('/{pengaturan}', [PengaturanWaktuController::class, 'update'])->name('update');
        Route::delete('/{pengaturan}', [PengaturanWaktuController::class, 'destroy'])->name('destroy');
    });
});