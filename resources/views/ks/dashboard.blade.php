@extends('layouts.app')
@section('title', 'Dashboard Kepala Sekolah')
@section('page-title', 'Dashboard')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Kepala Sekolah</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i><span class="text-slate-700 dark:text-zinc-200 font-medium">Dashboard</span>
@endsection

@section('content')
<!-- Stat Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card p-5">
        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/40 flex items-center justify-center mb-3">
            <i data-lucide="users" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
        </div>
        <p class="text-3xl font-bold text-slate-800 dark:text-white">{{ $totalGuru }}</p>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Total Guru Aktif</p>
    </div>
    <div class="card p-5">
        <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-950/40 flex items-center justify-center mb-3">
            <i data-lucide="user-x" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
        </div>
        <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $belumIsiHariIni->count() }}</p>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Guru belum isi hari ini</p>
    </div>
    <div class="card p-5">
        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center mb-3">
            <i data-lucide="notebook-pen" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
        </div>
        <p class="text-3xl font-bold text-slate-800 dark:text-white">{{ $jurnalHariIni }}</p>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Jurnal masuk hari ini</p>
    </div>
    <div class="card p-5">
        <div class="w-10 h-10 rounded-xl bg-green-50 dark:bg-green-950/40 flex items-center justify-center mb-3">
            <i data-lucide="calendar-check" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
        </div>
        <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $jurnalBulanIni }}</p>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Jurnal bulan ini</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <!-- Guru Belum Isi -->
    <div class="card">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-zinc-700/50">
            <div class="flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-500"></i>
                <h3 class="font-semibold text-sm text-slate-800 dark:text-white">Belum Isi Hari Ini</h3>
            </div>
            @if($belumIsiHariIni->isEmpty())
            <span class="badge badge-validated">Semua isi ✓</span>
            @endif
        </div>
        @if($belumIsiHariIni->isEmpty())
        <div class="flex flex-col items-center justify-center py-8 text-slate-400 dark:text-zinc-600">
            <i data-lucide="party-popper" class="w-8 h-8 mb-2 opacity-50"></i>
            <p class="text-xs">Semua guru sudah mengisi jurnal</p>
        </div>
        @else
        <div class="divide-y divide-slate-100 dark:divide-zinc-700/50">
            @foreach($belumIsiHariIni as $guru)
            <div class="flex items-center gap-3 px-5 py-3">
                <div class="w-7 h-7 rounded-lg bg-red-100 dark:bg-red-950/40 flex items-center justify-center text-red-600 dark:text-red-400 text-xs font-bold flex-shrink-0">
                    {{ strtoupper(substr($guru->nama, 0, 1)) }}
                </div>
                <p class="text-sm text-slate-600 dark:text-zinc-400 truncate">{{ $guru->nama }}</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Jurnal Terbaru -->
    <div class="card">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-zinc-700/50">
            <div class="flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-amber-500"></i>
                <h3 class="font-semibold text-sm text-slate-800 dark:text-white">Jurnal Terbaru</h3>
            </div>
            <a href="{{ route('ks.jurnal.index') }}" class="text-xs text-amber-600 dark:text-amber-400 hover:underline">Lihat semua</a>
        </div>
        @if($jurnalTerbaru->isEmpty())
        <div class="flex flex-col items-center justify-center py-8 text-slate-400 dark:text-zinc-600">
            <i data-lucide="notebook" class="w-8 h-8 mb-2 opacity-50"></i>
            <p class="text-xs">Belum ada jurnal</p>
        </div>
        @else
        <div class="divide-y divide-slate-100 dark:divide-zinc-700/50">
            @foreach($jurnalTerbaru as $j)
            <div class="flex items-center gap-3 px-5 py-3">
                <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center text-amber-600 dark:text-amber-400 text-xs font-bold flex-shrink-0">
                    {{ strtoupper(substr($j->guru->nama, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $j->guru->nama }}</p>
                    <p class="text-[10px] text-slate-400">{{ $j->mapel->nama }} · {{ $j->kelas->nama }} · {{ $j->tanggal->translatedFormat('j M') }}</p>
                </div>
                @if($j->is_terlambat)
                <span class="text-[10px] text-red-500 dark:text-red-400 font-medium flex-shrink-0">+{{ $j->menit_terlambat }} mnt</span>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
