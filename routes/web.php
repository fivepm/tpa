<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\Auth\WebAuthnController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Pengurus\GuruController;
use App\Http\Controllers\Pengurus\PengurusController;
use App\Http\Controllers\Pengurus\OrangtuaController;
use App\Http\Controllers\Pengurus\KelasController;
use App\Http\Controllers\Pengurus\SiswaController;
use App\Http\Controllers\Pengurus\MateriController;
use App\Http\Controllers\Pengurus\JadwalController;
use App\Http\Controllers\Pengurus\HariLiburController;
use App\Http\Controllers\Pengurus\LaporanController;
use App\Http\Controllers\Pengurus\RekapitulasiJurnalController;
use App\Http\Controllers\Pengurus\WaliKelasController;
use App\Http\Controllers\Pengurus\DashboardController as PengurusDashboardController;

use App\Http\Controllers\Guru\PresensiController;
use App\Http\Controllers\Guru\PerkembanganController;
use App\Http\Controllers\Guru\JurnalController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;

use App\Http\Controllers\OrangTua\DashboardController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', function () {})->middleware(['auth', 'verified', 'role.redirect'])->name('dashboard');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Profil Pengguna (baru)
    Route::get('/my-profile', [UserProfileController::class, 'index'])->name('profile.index');
    Route::post('/my-profile/password', [UserProfileController::class, 'updatePassword'])->name('profile.password');

    // WebAuthn — harus auth
    Route::get('/webauthn/register/options', [WebAuthnController::class, 'registerOptions'])->name('webauthn.register.options');
    Route::post('/webauthn/register', [WebAuthnController::class, 'register'])->name('webauthn.register');
    Route::delete('/webauthn/{credential}', [WebAuthnController::class, 'destroy'])->name('webauthn.destroy');

    // Group Pengurus
    Route::prefix('pengurus')->name('pengurus.')->group(function () {
        Route::get('/dashboard', [PengurusDashboardController::class, 'index'])->name('dashboard');
        Route::resource('kelola-guru', GuruController::class);
        Route::resource('kelola-pengurus', PengurusController::class);
        Route::resource('kelola-orangtua', OrangtuaController::class);
        Route::resource('kelola-kelas', KelasController::class);
        Route::resource('kelola-siswa', SiswaController::class);
        Route::resource('kelola-materi', MateriController::class);
        Route::resource('kelola-jadwal', JadwalController::class);
        Route::get('/api/kelas/{kelas}/guru', [JadwalController::class, 'getGuruByKelas'])->name('api.kelas.guru');
        Route::resource('kelola-harilibur', HariLiburController::class);
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/kelas/{kelas}', [LaporanController::class, 'showKelas'])->name('laporan.showKelas');
        Route::get('/laporan/siswa/{siswa}', [LaporanController::class, 'showSiswa'])->name('laporan.showSiswa');
        Route::get('/laporan/kelas/{kelas}/pdf', [LaporanController::class, 'exportKelasPdf'])->name('laporan.exportKelasPdf');

        // Rekapitulasi Jurnal Perkembangan
        Route::get('/rekap-jurnal', [RekapitulasiJurnalController::class, 'index'])->name('rekap-jurnal.index');
        Route::get('/rekap-jurnal/kelas/{kelas}', [RekapitulasiJurnalController::class, 'showKelas'])->name('rekap-jurnal.show-kelas');

        // Wali Kelas
        Route::get('/kelola-walikelas', [WaliKelasController::class, 'index'])->name('kelola-walikelas.index');
        Route::get('/kelola-walikelas/create', [WaliKelasController::class, 'create'])->name('kelola-walikelas.create');
        Route::post('/kelola-walikelas', [WaliKelasController::class, 'store'])->name('kelola-walikelas.store');
        Route::delete('/kelola-walikelas/{waliKelas}', [WaliKelasController::class, 'destroy'])->name('kelola-walikelas.destroy');
        Route::get('/api/walikelas/kelas/{kelas}/guru', [WaliKelasController::class, 'getGuruByKelas'])->name('api.walikelas.guru');
    });

    // Group Guru
    Route::prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');
        Route::get('/presensi', [PresensiController::class, 'index'])->name('presensi.index');
        Route::get('/presensi/kelas/{kelas}', [PresensiController::class, 'show'])->name('presensi.show');
        Route::post('/presensi/kelas/{kelas}', [PresensiController::class, 'store'])->name('presensi.store');
        Route::get('/perkembangan', [PerkembanganController::class, 'index'])->name('perkembangan.index');
        Route::get('/perkembangan', [PerkembanganController::class, 'index'])->name('perkembangan.index');
        Route::get('/perkembangan/{jadwal}/create', [PerkembanganController::class, 'create'])->name('perkembangan.create');
        Route::post('/perkembangan/{jadwal}', [PerkembanganController::class, 'store'])->name('perkembangan.store');
        Route::get('/perkembangan/{jadwal}/edit', [PerkembanganController::class, 'edit'])->name('perkembangan.edit');
        Route::put('/perkembangan/{jadwal}', [PerkembanganController::class, 'update'])->name('perkembangan.update');
        Route::delete('/perkembangan/{perkembangan}', [PerkembanganController::class, 'destroy'])->name('perkembangan.destroy');

        // Jurnal Mengajar
        Route::get('/jurnal', [JurnalController::class, 'index'])->name('jurnal.index');
        Route::get('/jurnal/{jadwal}/create', [JurnalController::class, 'create'])->name('jurnal.create');
        Route::post('/jurnal/{jadwal}', [JurnalController::class, 'store'])->name('jurnal.store');
        Route::get('/jurnal/{jadwal}/edit', [JurnalController::class, 'edit'])->name('jurnal.edit');
        Route::put('/jurnal/{jadwal}', [JurnalController::class, 'update'])->name('jurnal.update');
    });

    // Group Orang Tua
    Route::prefix('orangtua')->name('orangtua.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
});

require __DIR__ . '/auth.php';
