@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i>
    <span class="text-slate-700 dark:text-zinc-200 font-medium">Dashboard Admin</span>
@endsection

@section('content')
@php
    $jam = now()->hour;
    $greeting = $jam < 11 ? 'Selamat Pagi' : ($jam < 15 ? 'Selamat Siang' : 'Selamat Sore');

    $jamNow = now()->format('H:i:s');
    $sedangMengajar = $jadwalHariIni->filter(
        fn($j) => $j->jam_mulai <= $jamNow && $j->jam_selesai > $jamNow
    );
@endphp

{{-- Section 1: Context Banner --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800 dark:text-white">{{ $greeting }}</h2>
        <p class="text-sm text-slate-400 dark:text-zinc-500 mt-0.5">
            {{ now()->translatedFormat('l, d F Y') }}
        </p>
    </div>
    @if($sekolah)
    <div class="flex items-center gap-3 bg-amber-50 dark:bg-amber-950/20 border border-amber-200/70 dark:border-amber-800/40 rounded-2xl px-4 py-3 flex-shrink-0">
        <div class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-950/60 flex items-center justify-center flex-shrink-0">
            <i data-lucide="building-2" class="w-4 h-4 text-amber-600 dark:text-amber-400"></i>
        </div>
        <div class="min-w-0">
            <p class="text-sm font-bold text-slate-800 dark:text-white truncate">{{ $sekolah->nama }}</p>
            <div class="flex items-center gap-2 mt-0.5">
                @if($sekolah->npsn)
                <p class="text-xs text-slate-400 dark:text-zinc-500">NPSN: {{ $sekolah->npsn }}</p>
                @endif
                @if($tahunAktif)
                <span class="badge badge-validated text-[10px]">{{ $tahunAktif->nama }} — {{ $tahunAktif->semester }}</span>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Section 2: Stat Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card p-5">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/40 flex items-center justify-center flex-shrink-0">
                <i data-lucide="users" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
            </div>
            <span class="text-xs text-slate-400 dark:text-zinc-500 font-medium text-right">Total Guru</span>
        </div>
        <p class="text-3xl font-bold text-slate-800 dark:text-white">{{ $totalGuru }}</p>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1 mb-3">Guru aktif terdaftar</p>
        <a href="{{ route('admin.users.index') }}" class="text-xs text-blue-600 dark:text-blue-400 font-medium hover:underline flex items-center gap-1">
            Kelola <i data-lucide="arrow-right" class="w-3 h-3"></i>
        </a>
    </div>

    <div class="card p-5">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/40 flex items-center justify-center flex-shrink-0">
                <i data-lucide="school" class="w-5 h-5 text-purple-600 dark:text-purple-400"></i>
            </div>
            <span class="text-xs text-slate-400 dark:text-zinc-500 font-medium text-right">Total Kelas</span>
        </div>
        <p class="text-3xl font-bold text-slate-800 dark:text-white">{{ $totalKelas }}</p>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1 mb-3">Kelas tersedia</p>
        <a href="{{ route('admin.kelas.index') }}" class="text-xs text-purple-600 dark:text-purple-400 font-medium hover:underline flex items-center gap-1">
            Kelola <i data-lucide="arrow-right" class="w-3 h-3"></i>
        </a>
    </div>

    <div class="card p-5">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center flex-shrink-0">
                <i data-lucide="book-marked" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
            </div>
            <span class="text-xs text-slate-400 dark:text-zinc-500 font-medium text-right">Total Mapel</span>
        </div>
        <p class="text-3xl font-bold text-slate-800 dark:text-white">{{ $totalMapel }}</p>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1 mb-3">Mata pelajaran</p>
        <a href="{{ route('admin.mapel.index') }}" class="text-xs text-emerald-600 dark:text-emerald-400 font-medium hover:underline flex items-center gap-1">
            Kelola <i data-lucide="arrow-right" class="w-3 h-3"></i>
        </a>
    </div>

    <div class="card p-5">
        <div class="flex items-start justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center flex-shrink-0">
                <i data-lucide="calendar-clock" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
            </div>
            <span class="text-xs text-slate-400 dark:text-zinc-500 font-medium text-right">Total Jadwal</span>
        </div>
        <p class="text-3xl font-bold text-slate-800 dark:text-white">{{ $totalJadwal }}</p>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1 mb-3">Jadwal mengajar</p>
        <a href="{{ route('admin.jadwal.index') }}" class="text-xs text-amber-600 dark:text-amber-400 font-medium hover:underline flex items-center gap-1">
            Kelola <i data-lucide="arrow-right" class="w-3 h-3"></i>
        </a>
    </div>
</div>

{{-- Section 3: Operasional Hari Ini --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

    {{-- Mengajar Sekarang --}}
    <div class="card overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-zinc-700/50">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center">
                    <i data-lucide="radio" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <span class="text-sm font-semibold text-slate-800 dark:text-white">Mengajar Sekarang</span>
            </div>
            <span class="badge {{ $sedangMengajar->isNotEmpty() ? 'bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400' : 'bg-slate-100 dark:bg-zinc-700 text-slate-500 dark:text-zinc-400' }}">
                {{ $sedangMengajar->count() }} guru
            </span>
        </div>
        @if($sedangMengajar->isEmpty())
        <div class="flex flex-col items-center justify-center py-8 text-slate-400 dark:text-zinc-600">
            <i data-lucide="coffee" class="w-8 h-8 mb-2 opacity-40"></i>
            <p class="text-xs">Tidak ada sesi aktif saat ini</p>
        </div>
        @else
        <div class="divide-y divide-slate-100 dark:divide-zinc-700/50">
            @foreach($sedangMengajar->take(5) as $j)
            <div class="flex items-center gap-3 px-5 py-3">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 flex-shrink-0 animate-pulse"></span>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $j->guru->nama }}</p>
                    <p class="text-[10px] text-slate-400 dark:text-zinc-500 truncate">{{ $j->mapel->nama }} · Kelas {{ $j->kelas->nama }}</p>
                </div>
                <span class="text-[10px] font-mono text-slate-400 dark:text-zinc-500 flex-shrink-0">{{ substr($j->jam_mulai,0,5) }}–{{ substr($j->jam_selesai,0,5) }}</span>
            </div>
            @endforeach
            @if($sedangMengajar->count() > 5)
            <div class="px-5 py-2.5 text-xs text-slate-400 dark:text-zinc-500">
                +{{ $sedangMengajar->count() - 5 }} guru lagi
            </div>
            @endif
        </div>
        @endif
    </div>

    {{-- Guru Belum Isi Jurnal --}}
    <div class="card overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-zinc-700/50">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg {{ $guruBelumIsi->isEmpty() ? 'bg-emerald-50 dark:bg-emerald-950/40' : 'bg-yellow-50 dark:bg-yellow-950/40' }} flex items-center justify-center">
                    <i data-lucide="{{ $guruBelumIsi->isEmpty() ? 'check-circle-2' : 'alert-circle' }}"
                       class="w-3.5 h-3.5 {{ $guruBelumIsi->isEmpty() ? 'text-emerald-600 dark:text-emerald-400' : 'text-yellow-500 dark:text-yellow-400' }}"></i>
                </div>
                <span class="text-sm font-semibold text-slate-800 dark:text-white">Belum Isi Jurnal</span>
            </div>
            <span class="badge {{ $guruBelumIsi->isEmpty() ? 'bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400' : 'bg-yellow-100 dark:bg-yellow-950/40 text-yellow-700 dark:text-yellow-400' }}">
                {{ $guruBelumIsi->count() }} guru
            </span>
        </div>
        @if($guruBelumIsi->isEmpty())
        <div class="flex flex-col items-center justify-center py-8 text-emerald-500 dark:text-emerald-400">
            <i data-lucide="check-circle-2" class="w-8 h-8 mb-2 opacity-60"></i>
            <p class="text-xs font-medium">Semua sudah mengisi</p>
        </div>
        @else
        <div class="divide-y divide-slate-100 dark:divide-zinc-700/50">
            @foreach($guruBelumIsi->take(5) as $guruId => $jadwalGuru)
            @php $namaGuru = $jadwalGuru->first()->guru->nama; @endphp
            <div class="flex items-center gap-3 px-5 py-3">
                <div class="w-6 h-6 rounded-lg bg-yellow-50 dark:bg-yellow-950/40 flex items-center justify-center flex-shrink-0">
                    <span class="text-[10px] font-bold text-yellow-600 dark:text-yellow-400">{{ strtoupper(substr($namaGuru, 0, 1)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-slate-700 dark:text-slate-200 truncate">{{ $namaGuru }}</p>
                    <p class="text-[10px] text-slate-400 dark:text-zinc-500">{{ $jadwalGuru->count() }} sesi hari ini</p>
                </div>
            </div>
            @endforeach
            @if($guruBelumIsi->count() > 5)
            <div class="px-5 py-2.5 text-xs text-slate-400 dark:text-zinc-500">
                +{{ $guruBelumIsi->count() - 5 }} guru lagi
            </div>
            @endif
        </div>
        @endif
    </div>

</div>

{{-- Section 4: Aksi Cepat --}}
<div class="card p-5 mb-6">
    <h3 class="text-sm font-semibold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
        <i data-lucide="zap" class="w-4 h-4 text-amber-500"></i>
        Aksi Cepat
    </h3>

    {{-- 3 Card Jadwal Besar --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
        <a href="{{ route('admin.jadwal.index') }}"
           class="card p-4 flex items-start gap-3 hover:border-amber-300 dark:hover:border-amber-700 hover:shadow-md transition-all">
            <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center flex-shrink-0">
                <i data-lucide="calendar-clock" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-800 dark:text-white">Kelola Jadwal</p>
                <p class="text-xs text-slate-400 dark:text-zinc-500 mt-0.5">Tambah & atur jadwal mengajar</p>
            </div>
        </a>

        <a href="{{ route('admin.jadwal.by-guru') }}"
           class="card p-4 flex items-start gap-3 hover:border-blue-300 dark:hover:border-blue-700 hover:shadow-md transition-all">
            <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/40 flex items-center justify-center flex-shrink-0">
                <i data-lucide="user-check" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-800 dark:text-white">Jadwal per Guru</p>
                <p class="text-xs text-slate-400 dark:text-zinc-500 mt-0.5">Lihat jadwal tiap guru</p>
            </div>
        </a>

        <a href="{{ route('admin.jadwal.index') }}"
           class="card p-4 flex items-start gap-3 hover:border-purple-300 dark:hover:border-purple-700 hover:shadow-md transition-all">
            <div class="w-9 h-9 rounded-xl bg-purple-50 dark:bg-purple-950/40 flex items-center justify-center flex-shrink-0">
                <i data-lucide="layout-list" class="w-5 h-5 text-purple-600 dark:text-purple-400"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-800 dark:text-white">Jadwal per Kelas</p>
                <p class="text-xs text-slate-400 dark:text-zinc-500 mt-0.5">Lihat jadwal tiap kelas</p>
            </div>
        </a>
    </div>

    {{-- Tombol Secondary --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.users.index') }}" class="btn-secondary text-xs">
            <i data-lucide="user-plus" class="w-3.5 h-3.5"></i> Tambah Pengguna
        </a>
        <a href="{{ route('admin.kelas.index') }}" class="btn-secondary text-xs">
            <i data-lucide="school" class="w-3.5 h-3.5"></i> Kelola Kelas
        </a>
        <a href="{{ route('admin.sekolah.index') }}" class="btn-secondary text-xs">
            <i data-lucide="settings" class="w-3.5 h-3.5"></i> Identitas Sekolah
        </a>
        <a href="{{ route('admin.jadwal.print.semua') }}" target="_blank" class="btn-secondary text-xs">
            <i data-lucide="printer" class="w-3.5 h-3.5"></i> Cetak Semua Jadwal
        </a>
    </div>
</div>

@endsection
