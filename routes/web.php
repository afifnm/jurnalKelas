<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\ImportTemplateController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Guru;
use App\Http\Controllers\Ks;
use App\Http\Controllers\Admin\JadwalViewController as AdminJadwalViewController;
use App\Http\Controllers\Ks\JadwalViewController as KsJadwalViewController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// Offline page (PWA)
Route::get('/offline', fn() => view('offline'))->name('offline');

// Auth
Route::get('/login',  [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post')
    ->middleware('throttle:10,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Dashboard redirect
Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// Panduan penggunaan (all roles)
Route::get('/panduan', fn() => view('panduan'))->name('panduan')->middleware('auth');

// Lampiran private access via signed URL
Route::get('/lampiran/{path}', function (string $path) {
    if (! request()->hasValidSignature()) {
        abort(403);
    }
    if (! Storage::disk('local')->exists($path)) abort(404);
    return response()->file(Storage::disk('local')->path($path));
})->where('path', '.*')->name('lampiran.show');

// --- ADMIN ---
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard',  [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::post('users/import',  [Admin\UserController::class, 'import'])->name('users.import');
    Route::get('users/template', [ImportTemplateController::class, 'pengguna'])->name('users.import.template');
    Route::resource('users', Admin\UserController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('users/{id}/restore', [Admin\UserController::class, 'restore'])->name('users.restore');
    Route::post('users/{id}/reset-password', [Admin\UserController::class, 'resetPassword'])->name('users.reset-password');

    Route::post('kelas/import',  [Admin\KelasController::class, 'import'])->name('kelas.import');
    Route::get('kelas/template', [ImportTemplateController::class, 'kelas'])->name('kelas.import.template');
    Route::resource('kelas', Admin\KelasController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::post('mapel/import',  [Admin\MapelController::class, 'import'])->name('mapel.import');
    Route::get('mapel/template', [ImportTemplateController::class, 'mapel'])->name('mapel.import.template');
    Route::get('mapel/{mapel}/pengajar', [Admin\MapelController::class, 'pengajar'])->name('mapel.pengajar');
    Route::resource('mapel', Admin\MapelController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('jam-pelajaran', [Admin\JamPelajaranController::class, 'index'])->name('jam-pelajaran.index');
    Route::post('jam-pelajaran/clone', [Admin\JamPelajaranController::class, 'cloneHari'])->name('jam-pelajaran.clone');
    Route::post('jam-pelajaran', [Admin\JamPelajaranController::class, 'store'])->name('jam-pelajaran.store');
    Route::put('jam-pelajaran/{id}', [Admin\JamPelajaranController::class, 'update'])->name('jam-pelajaran.update');
    Route::delete('jam-pelajaran/{id}', [Admin\JamPelajaranController::class, 'destroy'])->name('jam-pelajaran.destroy');

    Route::get('/jadwal',              [AdminJadwalViewController::class, 'byKelas'])->name('jadwal.index');
    Route::get('/jadwal/guru',         [AdminJadwalViewController::class, 'byGuru'])->name('jadwal.by-guru');
    Route::get('/jadwal/mapping',      [AdminJadwalViewController::class, 'mapping'])->name('jadwal.mapping');
    Route::get('/jadwal/print/semua',  [AdminJadwalViewController::class, 'printSemua'])->name('jadwal.print.semua');
    Route::get('/jadwal/print/guru/{guru}',   [AdminJadwalViewController::class, 'printGuru'])->name('jadwal.print.guru');
    Route::get('/jadwal/print/kelas/{kelas}', [AdminJadwalViewController::class, 'printKelas'])->name('jadwal.print.kelas');
    Route::get('/jadwal/print/laporan-jurnal/{kelas}', [AdminJadwalViewController::class, 'laporanJurnalKelas'])->name('jadwal.print.laporan-jurnal-kelas');
    Route::get('/jadwal/print/laporan-jurnal-guru/{guru}', [AdminJadwalViewController::class, 'laporanJurnalGuru'])->name('jadwal.print.laporan-jurnal-guru');
    Route::resource('jadwal', Admin\JadwalController::class)->only(['store', 'update', 'destroy']);

    Route::resource('tahun-ajaran', Admin\TahunAjaranController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['tahun-ajaran' => 'tahunAjaran']);
    Route::patch('tahun-ajaran/{tahunAjaran}/aktivasi', [Admin\TahunAjaranController::class, 'aktivasi'])
        ->name('tahun-ajaran.aktivasi');
    Route::post('tahun-ajaran/{tahunAjaran}/clone-jadwal', [Admin\TahunAjaranController::class, 'cloneJadwal'])
        ->name('tahun-ajaran.clone-jadwal');
    Route::post('tahun-ajaran/{tahunAjaran}/generate-jadwal', [Admin\TahunAjaranController::class, 'generateJadwal'])
        ->name('tahun-ajaran.generate-jadwal');

    // Pembagian Tugas Mengajar
    Route::get('/tugas-mengajar/print', [Admin\TugasMengajarController::class, 'print'])->name('tugas-mengajar.print');
    Route::get('/tugas-mengajar', [Admin\TugasMengajarController::class, 'index'])->name('tugas-mengajar.index');
    Route::post('/tugas-mengajar/cell', [Admin\TugasMengajarController::class, 'updateCell'])->name('tugas-mengajar.update-cell');
    Route::post('/tugas-mengajar/jabatan', [Admin\TugasMengajarController::class, 'updateJabatan'])->name('tugas-mengajar.update-jabatan');
    Route::post('/tugas-mengajar/destroy-row', [Admin\TugasMengajarController::class, 'destroyRow'])->name('tugas-mengajar.destroy-row');
    Route::post('/tugas-mengajar/bulk-update', [Admin\TugasMengajarController::class, 'bulkUpdate'])->name('tugas-mengajar.bulk-update');

    Route::get('/sekolah',   [Admin\SekolahController::class, 'index'])->name('sekolah.index');
    Route::put('/sekolah',   [Admin\SekolahController::class, 'update'])->name('sekolah.update');

    Route::get('/jurnal',         [Admin\JurnalController::class, 'index'])->name('jurnal.index');
    Route::get('/jurnal/{jurnal}', [Admin\JurnalController::class, 'show'])->name('jurnal.show');
});

// --- GURU ---
Route::prefix('guru')->name('guru.')->middleware(['auth', 'role:guru'])->group(function () {
    Route::get('/dashboard', [Guru\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/jadwal',          [Guru\JadwalViewController::class, 'byGuru'])->name('jadwal.index');
    Route::get('/jadwal/kelas',    [Guru\JadwalViewController::class, 'byKelas'])->name('jadwal.by-kelas');
    Route::get('/jadwal/print/guru/{guru}',           [Guru\JadwalViewController::class, 'printGuru'])->name('jadwal.print.guru');
    Route::get('/jadwal/print/laporan-jurnal-guru/{guru}', [Guru\JadwalViewController::class, 'laporanJurnalGuru'])->name('jadwal.print.laporan-jurnal-guru');
    Route::get('/jadwal/print/kelas/{kelas}',         [Guru\JadwalViewController::class, 'printKelas'])->name('jadwal.print.kelas');
    Route::get('/jadwal/print/laporan-jurnal-kelas/{kelas}', [Guru\JadwalViewController::class, 'laporanJurnalKelas'])->name('jadwal.print.laporan-jurnal-kelas');

    Route::resource('jurnal', Guru\JurnalController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::get('/jurnal/{jurnal}/show', [Guru\JurnalController::class, 'show'])->name('jurnal.show');
    Route::delete('/jurnal/lampiran/{lampiran}', [Guru\JurnalController::class, 'hapusLampiran'])
        ->name('jurnal.lampiran.hapus');
});

// --- KEPALA SEKOLAH ---
Route::prefix('ks')->name('ks.')->middleware(['auth', 'role:ks'])->group(function () {
    Route::get('/dashboard', [Ks\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/jurnal',          [Ks\JurnalController::class, 'index'])->name('jurnal.index');
    Route::get('/jurnal/{jurnal}', [Ks\JurnalController::class, 'show'])->name('jurnal.show');

    Route::get('/jadwal/kelas', [KsJadwalViewController::class, 'byKelas'])->name('jadwal.by-kelas');
    Route::get('/jadwal/guru',  [KsJadwalViewController::class, 'byGuru'])->name('jadwal.by-guru');
});
