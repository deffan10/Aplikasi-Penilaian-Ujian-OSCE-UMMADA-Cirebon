<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\StasiController;
use App\Http\Controllers\Admin\KomponenStasiController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\PengujiController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\RekapController;
use App\Http\Controllers\Admin\GelombangController;
use App\Http\Controllers\Admin\JadwalPengujiController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Penguji\DashboardController as PengujiDashboardController;
use App\Http\Controllers\Penguji\PenilaianController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Redirect dashboard based on role
Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('penguji.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ==================== ADMIN ROUTES ====================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Stasi Management
    Route::resource('stasi', StasiController::class)->except(['show']);
    
    // Komponen Stasi (nested under stasi)
    Route::get('stasi/{stasi}/komponen', [KomponenStasiController::class, 'index'])->name('stasi.komponen.index');
    Route::post('stasi/{stasi}/komponen', [KomponenStasiController::class, 'store'])->name('stasi.komponen.store');
    Route::put('stasi/{stasi}/komponen/{komponen}', [KomponenStasiController::class, 'update'])->name('stasi.komponen.update');
    Route::delete('stasi/{stasi}/komponen/{komponen}', [KomponenStasiController::class, 'destroy'])->name('stasi.komponen.destroy');

    // Kelas Management
    Route::get('kelas/arsip', [KelasController::class, 'arsip'])->name('kelas.arsip');
    Route::put('kelas/{kela}/arsipkan', [KelasController::class, 'arsipkan'])->name('kelas.arsipkan');
    Route::put('kelas/{kela}/restore', [KelasController::class, 'restore'])->name('kelas.restore');
    Route::resource('kelas', KelasController::class)->except(['show']);

    // Mahasiswa Management
    Route::get('mahasiswa/import', [MahasiswaController::class, 'importForm'])->name('mahasiswa.import.form');
    Route::post('mahasiswa/import', [MahasiswaController::class, 'import'])->name('mahasiswa.import');
    Route::resource('mahasiswa', MahasiswaController::class)->except(['show']);

    // Penguji Management
    Route::get('penguji/import', [PengujiController::class, 'importForm'])->name('penguji.import-form');
    Route::post('penguji/import', [PengujiController::class, 'import'])->name('penguji.import');
    Route::get('penguji/download-template', [PengujiController::class, 'downloadTemplate'])->name('penguji.download-template');
    Route::get('penguji/print-labels', [PengujiController::class, 'printLabels'])->name('penguji.print-labels');
    Route::resource('penguji', PengujiController::class)->except(['show']);

    // Jadwal Management
    Route::get('jadwal/arsip', [JadwalController::class, 'arsip'])->name('jadwal.arsip');
    Route::put('jadwal/{jadwal}/arsipkan', [JadwalController::class, 'arsipkan'])->name('jadwal.arsipkan');
    Route::put('jadwal/{jadwal}/restore', [JadwalController::class, 'restore'])->name('jadwal.restore');
    Route::resource('jadwal', JadwalController::class);

    // Gelombang Management
    Route::get('gelombang', [GelombangController::class, 'list'])->name('gelombang.index');
    Route::get('jadwal/{jadwal}/gelombang', [GelombangController::class, 'index'])->name('jadwal.gelombang.index');
    Route::get('jadwal/{jadwal}/gelombang/create', [GelombangController::class, 'create'])->name('jadwal.gelombang.create');
    Route::post('jadwal/{jadwal}/gelombang', [GelombangController::class, 'store'])->name('jadwal.gelombang.store');
    Route::get('jadwal/{jadwal}/gelombang/{gelombang}/edit', [GelombangController::class, 'edit'])->name('jadwal.gelombang.edit');
    Route::put('jadwal/{jadwal}/gelombang/{gelombang}', [GelombangController::class, 'update'])->name('jadwal.gelombang.update');
    Route::delete('jadwal/{jadwal}/gelombang/{gelombang}', [GelombangController::class, 'destroy'])->name('jadwal.gelombang.destroy');

    // Jadwal Penguji (Plotting penguji ke stasi per gelombang)
    Route::get('jadwal-penguji', [JadwalPengujiController::class, 'index'])->name('jadwal-penguji.index');
    Route::get('jadwal-penguji/{jadwal}/gelombang', [JadwalPengujiController::class, 'gelombang'])->name('jadwal-penguji.gelombang');
    Route::get('jadwal-penguji/{jadwal}/gelombang/create', [JadwalPengujiController::class, 'createGelombang'])->name('jadwal-penguji.create-gelombang');
    Route::post('jadwal-penguji/{jadwal}/gelombang', [JadwalPengujiController::class, 'storeGelombang'])->name('jadwal-penguji.store-gelombang');
    Route::delete('jadwal-penguji/{jadwal}/gelombang/{gelombang}', [JadwalPengujiController::class, 'destroyGelombang'])->name('jadwal-penguji.destroy-gelombang');
    Route::get('jadwal-penguji/{jadwal}/gelombang/{gelombang}/assign', [JadwalPengujiController::class, 'assign'])->name('jadwal-penguji.assign');
    Route::post('jadwal-penguji/{jadwal}/gelombang/{gelombang}/assign', [JadwalPengujiController::class, 'storeAssignment'])->name('jadwal-penguji.store-assignment');
    Route::get('jadwal-penguji/{jadwal}/gelombang/{gelombang}/mahasiswa', [JadwalPengujiController::class, 'assignMahasiswa'])->name('jadwal-penguji.assign-mahasiswa');
    Route::post('jadwal-penguji/{jadwal}/gelombang/{gelombang}/mahasiswa', [JadwalPengujiController::class, 'storeMahasiswa'])->name('jadwal-penguji.store-mahasiswa');
    Route::post('jadwal-penguji/{jadwal}/copy-assignment', [JadwalPengujiController::class, 'copyAssignment'])->name('jadwal-penguji.copy-assignment');

    // Rekap & Export
    Route::get('rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::get('rekap/jadwal/{jadwal}', [RekapController::class, 'perJadwal'])->name('rekap.jadwal');
    Route::get('rekap/jadwal/{jadwal}/pdf', [RekapController::class, 'exportPdfJadwal'])->name('rekap.jadwal.pdf');
    Route::get('rekap/jadwal/{jadwal}/excel', [RekapController::class, 'exportExcelJadwal'])->name('rekap.jadwal.excel');
    Route::post('rekap/jadwal/{jadwal}/hitung-nilai-acuan', [RekapController::class, 'hitungNilaiAcuan'])->name('rekap.jadwal.hitungNilaiAcuan');
    Route::get('rekap/stasi', [RekapController::class, 'perStasi'])->name('rekap.stasi');
    Route::get('rekap/kelas/{kelas}', [RekapController::class, 'perKelas'])->name('rekap.kelas');
    Route::get('rekap/kelas/{kelas}/pdf', [RekapController::class, 'exportPdfKelas'])->name('rekap.kelas.pdf');
    Route::get('rekap/kelas/{kelas}/excel', [RekapController::class, 'exportExcelKelas'])->name('rekap.kelas.excel');

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
});

// ==================== PENGUJI ROUTES ====================
Route::middleware(['auth'])->prefix('penguji')->name('penguji.')->group(function () {
    
    // Dashboard
    Route::get('/', [PengujiDashboardController::class, 'index'])->name('dashboard');

    // === NEW FLOW: Gelombang-based penilaian ===
    // Main penilaian index - list gelombang yang ditugaskan
    Route::get('penilaian', [PenilaianController::class, 'index'])->name('penilaian.index');
    // Select stasi in a gelombang
    Route::get('penilaian/gelombang/{gelombang}', [PenilaianController::class, 'selectStasi'])->name('penilaian.select-stasi');
    // List mahasiswa in gelombang+stasi
    Route::get('penilaian/gelombang/{gelombang}/stasi/{stasi}', [PenilaianController::class, 'listMahasiswa'])->name('penilaian.list');
    // Form penilaian
    Route::get('penilaian/gelombang/{gelombang}/stasi/{stasi}/mahasiswa/{mahasiswa}', [PenilaianController::class, 'form'])->name('penilaian.form');
    Route::post('penilaian/gelombang/{gelombang}/stasi/{stasi}/mahasiswa/{mahasiswa}', [PenilaianController::class, 'store'])->name('penilaian.store');

    // === LEGACY ROUTES (backward compatibility) ===
    Route::get('stasi', [PenilaianController::class, 'stasiIndex'])->name('stasi.index');
    Route::get('stasi/{stasi}', [PenilaianController::class, 'stasiShow'])->name('stasi.show');
    Route::get('penilaian/stasi/{stasi}', [PenilaianController::class, 'selectJadwal'])->name('penilaian.stasi');
    Route::get('penilaian/stasi/{stasi}/jadwal/{jadwal}', [PenilaianController::class, 'listMahasiswaLegacy'])->name('penilaian.list.legacy');
});

require __DIR__.'/auth.php';
