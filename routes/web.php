<?php

use App\Http\Controllers\Admin;
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

// Lampiran private access via signed URL
Route::get('/lampiran/{path}', function (string $path) {
    if (! request()->hasValidSignature()) {
        abort(403);
    }
    $fullPath = storage_path("app/lampiran/{$path}");
    if (! file_exists($fullPath)) abort(404);
    return response()->file($fullPath);
})->where('path', '.*')->name('lampiran.show');

// --- ADMIN ---
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard',  [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', Admin\UserController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('users/{id}/restore', [Admin\UserController::class, 'restore'])->name('users.restore');

    Route::resource('kelas', Admin\KelasController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('mapel', Admin\MapelController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/jadwal/kelas',  [AdminJadwalViewController::class, 'byKelas'])->name('jadwal.by-kelas');
    Route::get('/jadwal/guru',   [AdminJadwalViewController::class, 'byGuru'])->name('jadwal.by-guru');
    Route::resource('jadwal', Admin\JadwalController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::resource('tahun-ajaran', Admin\TahunAjaranController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['tahun-ajaran' => 'tahunAjaran']);
    Route::patch('tahun-ajaran/{tahunAjaran}/aktivasi', [Admin\TahunAjaranController::class, 'aktivasi'])
        ->name('tahun-ajaran.aktivasi');

    Route::get('/sekolah',   [Admin\SekolahController::class, 'index'])->name('sekolah.index');
    Route::put('/sekolah',   [Admin\SekolahController::class, 'update'])->name('sekolah.update');

    Route::get('/jurnal',         [Admin\JurnalController::class, 'index'])->name('jurnal.index');
    Route::get('/jurnal/{jurnal}', [Admin\JurnalController::class, 'show'])->name('jurnal.show');
});

// --- GURU ---
Route::prefix('guru')->name('guru.')->middleware(['auth', 'role:guru'])->group(function () {
    Route::get('/dashboard', [Guru\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('jurnal', Guru\JurnalController::class)->only(['index', 'store', 'update', 'destroy']);
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
