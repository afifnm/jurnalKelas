@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i>
    <span>Dashboard Admin</span>
@endsection

@section('content')
<!-- Stat Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/40 flex items-center justify-center">
                <i data-lucide="users" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
            </div>
            <span class="text-xs text-slate-400 dark:text-zinc-500 font-medium">Total Guru</span>
        </div>
        <p class="text-3xl font-bold text-slate-800 dark:text-white">{{ $totalGuru }}</p>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Guru aktif terdaftar</p>
    </div>

    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/40 flex items-center justify-center">
                <i data-lucide="school" class="w-5 h-5 text-purple-600 dark:text-purple-400"></i>
            </div>
            <span class="text-xs text-slate-400 dark:text-zinc-500 font-medium">Total Kelas</span>
        </div>
        <p class="text-3xl font-bold text-slate-800 dark:text-white">{{ $totalKelas }}</p>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Kelas tersedia</p>
    </div>

    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center">
                <i data-lucide="book-marked" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
            </div>
            <span class="text-xs text-slate-400 dark:text-zinc-500 font-medium">Total Mapel</span>
        </div>
        <p class="text-3xl font-bold text-slate-800 dark:text-white">{{ $totalMapel }}</p>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Mata pelajaran</p>
    </div>

    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center">
                <i data-lucide="calendar-clock" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
            </div>
            <span class="text-xs text-slate-400 dark:text-zinc-500 font-medium">Total Jadwal</span>
        </div>
        <p class="text-3xl font-bold text-slate-800 dark:text-white">{{ $totalJadwal }}</p>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Jadwal mengajar</p>
    </div>
</div>

<!-- Row 2 -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <!-- Jadwal Hari Ini -->
    <div class="lg:col-span-2 card">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-zinc-700/50">
            <div class="flex items-center gap-2">
                <i data-lucide="calendar-days" class="w-4 h-4 text-amber-500"></i>
                <h3 class="font-semibold text-sm text-slate-800 dark:text-white">Jadwal Hari Ini</h3>
                <span class="text-xs text-slate-400 dark:text-zinc-500">({{ now()->translatedFormat('l, d M Y') }})</span>
            </div>
            <span class="badge bg-amber-100 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300">
                {{ $jadwalHariIni->count() }} sesi
            </span>
        </div>

        @if($jadwalHariIni->isEmpty())
        <div class="flex flex-col items-center justify-center py-12 text-slate-400 dark:text-zinc-600">
            <i data-lucide="calendar-off" class="w-10 h-10 mb-2 opacity-50"></i>
            <p class="text-sm">Tidak ada jadwal hari ini</p>
        </div>
        @else
        <div class="divide-y divide-slate-100 dark:divide-zinc-700/50">
            @foreach($jadwalHariIni as $j)
            <div class="flex items-center gap-4 px-5 py-3.5">
                <div class="text-center w-16 flex-shrink-0">
                    <p class="text-xs font-bold text-amber-600 dark:text-amber-400">{{ substr($j->jam_mulai, 0, 5) }}</p>
                    <p class="text-[10px] text-slate-400">{{ substr($j->jam_selesai, 0, 5) }}</p>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $j->guru->nama }}</p>
                    <p class="text-xs text-slate-400 dark:text-zinc-500">{{ $j->mapel->nama }} — {{ $j->kelas->nama }}</p>
                </div>
                <span class="badge bg-slate-100 dark:bg-zinc-700 text-slate-600 dark:text-zinc-300 text-xs">
                    {{ $j->kelas->nama }}
                </span>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Quick Info -->
    <div class="flex flex-col gap-4">
        <div class="card p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/40 flex items-center justify-center">
                    <i data-lucide="notebook-pen" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 dark:text-zinc-500">Jurnal Hari Ini</p>
                    <p class="text-xl font-bold text-slate-800 dark:text-white">{{ $jurnalHariIni }}</p>
                </div>
            </div>
            <a href="{{ route('admin.jadwal.index') }}" class="text-xs text-amber-600 dark:text-amber-400 font-medium hover:underline flex items-center gap-1">
                <span>Lihat jadwal</span><i data-lucide="arrow-right" class="w-3 h-3"></i>
            </a>
        </div>

        <div class="card p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-xl bg-orange-50 dark:bg-orange-950/40 flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4 text-orange-600 dark:text-orange-400"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 dark:text-zinc-500">Menunggu Validasi</p>
                    <p class="text-xl font-bold text-slate-800 dark:text-white">{{ $jurnalPending }}</p>
                </div>
            </div>
            <p class="text-xs text-slate-400 dark:text-zinc-500">Jurnal belum divalidasi</p>
        </div>

        @if($sekolah)
        <div class="card p-5">
            <div class="flex items-center gap-2 mb-2">
                <i data-lucide="building-2" class="w-4 h-4 text-slate-400"></i>
                <p class="text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Info Sekolah</p>
            </div>
            <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $sekolah->nama }}</p>
            <p class="text-xs text-slate-400 dark:text-zinc-500 mt-0.5">NPSN: {{ $sekolah->npsn ?? '-' }}</p>
            @if($tahunAktif)
            <span class="mt-2 inline-flex badge badge-validated">
                {{ $tahunAktif->nama }} — {{ $tahunAktif->semester }}
            </span>
            @endif
        </div>
        @endif
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-4 card p-5">
    <h3 class="text-sm font-semibold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
        <i data-lucide="zap" class="w-4 h-4 text-amber-500"></i>
        Aksi Cepat
    </h3>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.users.index') }}" class="btn-secondary text-xs">
            <i data-lucide="user-plus" class="w-3.5 h-3.5"></i> Tambah Pengguna
        </a>
        <a href="{{ route('admin.jadwal.index') }}" class="btn-secondary text-xs">
            <i data-lucide="calendar-plus" class="w-3.5 h-3.5"></i> Kelola Jadwal
        </a>
        <a href="{{ route('admin.kelas.index') }}" class="btn-secondary text-xs">
            <i data-lucide="school" class="w-3.5 h-3.5"></i> Kelola Kelas
        </a>
        <a href="{{ route('admin.sekolah.index') }}" class="btn-secondary text-xs">
            <i data-lucide="settings" class="w-3.5 h-3.5"></i> Identitas Sekolah
        </a>
    </div>
</div>
@endsection
