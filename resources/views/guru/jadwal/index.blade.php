@extends('layouts.app')
@section('title', 'Jadwal Mengajar')
@section('page-title', 'Jadwal Mengajar')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Guru</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i><span class="text-slate-700 dark:text-zinc-200 font-medium">Jadwal Mengajar</span>
@endsection

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card p-5">
        <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center mb-3">
            <i data-lucide="calendar-range" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
        </div>
        <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $totalSesi }}</p>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Total sesi/minggu</p>
    </div>
    <div class="card p-5">
        <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/40 flex items-center justify-center mb-3">
            <i data-lucide="calendar-days" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
        </div>
        <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $hariMengajar }}</p>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Hari mengajar</p>
    </div>
    <div class="card p-5 {{ $jadwalHariIni->isNotEmpty() ? 'border-amber-200 dark:border-amber-800/40' : '' }}">
        <div class="w-9 h-9 rounded-xl {{ $jadwalHariIni->isNotEmpty() ? 'bg-amber-50 dark:bg-amber-950/40' : 'bg-slate-50 dark:bg-zinc-800/60' }} flex items-center justify-center mb-3">
            <i data-lucide="clock" class="w-5 h-5 {{ $jadwalHariIni->isNotEmpty() ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400 dark:text-zinc-500' }}"></i>
        </div>
        <p class="text-2xl font-bold {{ $jadwalHariIni->isNotEmpty() ? 'text-amber-600 dark:text-amber-400' : 'text-slate-800 dark:text-white' }}">
            {{ $jadwalHariIni->count() }}
        </p>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Sesi hari ini</p>
    </div>
    <div class="card p-5">
        <div class="w-9 h-9 rounded-xl bg-green-50 dark:bg-green-950/40 flex items-center justify-center mb-3">
            <i data-lucide="school" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
        </div>
        <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $kelasUnik }}</p>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Kelas diampu</p>
    </div>
</div>

{{-- Jadwal Mingguan --}}
<div class="card overflow-hidden">
    <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 dark:border-zinc-700/50">
        <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center text-amber-600 dark:text-amber-400 text-sm font-bold flex-shrink-0">
            {{ strtoupper(substr($guru->nama, 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <h3 class="font-bold text-slate-800 dark:text-white truncate">{{ $guru->nama }}</h3>
            <p class="text-xs text-slate-400 dark:text-zinc-500">
                Jadwal mingguan
                @if($tahunAktif)
                    — {{ $tahunAktif->nama }} {{ $tahunAktif->semester }}
                @endif
            </p>
        </div>
        <a href="{{ route('guru.jurnal.create') }}"
           class="btn-primary text-xs py-1.5 flex-shrink-0">
            <i data-lucide="notebook-pen" class="w-3.5 h-3.5"></i>
            <span class="hidden sm:inline">Isi Jurnal</span>
        </a>
    </div>

    @if($jadwal->isEmpty())
    <div class="flex flex-col items-center justify-center py-16 text-slate-400 dark:text-zinc-600">
        <i data-lucide="calendar-x-2" class="w-12 h-12 mb-3 opacity-40"></i>
        <p class="text-sm font-medium">Belum ada jadwal mengajar</p>
        <p class="text-xs mt-1">Hubungi admin untuk mengatur jadwal Anda</p>
    </div>
    @else
    <div class="divide-y divide-slate-100 dark:divide-zinc-700/50">
        @foreach($namaHari as $hariNum => $hariNama)
        @php $jadwalHari = $jadwal->get($hariNum, collect()); @endphp
        @if($jadwalHari->isNotEmpty())
        <div class="{{ $hariNum == $hariIni ? 'bg-amber-50/50 dark:bg-amber-950/10' : '' }}">
            {{-- Header hari --}}
            <div class="flex items-center gap-2.5 px-5 py-2.5">
                <span class="text-xs font-bold uppercase tracking-wide w-12 flex-shrink-0
                    {{ $hariNum == $hariIni ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400 dark:text-zinc-500' }}">
                    {{ $hariNama }}
                </span>
                @if($hariNum == $hariIni)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 text-[10px] font-semibold rounded-full">
                    <i data-lucide="sun" class="w-2.5 h-2.5"></i> Hari ini
                </span>
                @endif
                <span class="ml-auto text-[10px] text-slate-400 dark:text-zinc-600">
                    {{ $jadwalHari->count() }} sesi
                </span>
            </div>

            {{-- List jadwal hari ini --}}
            <div class="space-y-1.5 px-4 pb-3">
                @foreach($jadwalHari->sortBy('jam_mulai') as $j)
                <div class="flex items-center gap-3 p-3 rounded-xl
                    {{ $hariNum == $hariIni
                        ? 'bg-white dark:bg-zinc-800/70 border border-amber-100 dark:border-amber-900/30 shadow-sm'
                        : 'bg-slate-50 dark:bg-zinc-800/40' }}">

                    {{-- Jam --}}
                    <div class="text-center w-[4.5rem] flex-shrink-0">
                        <p class="text-xs font-bold text-amber-600 dark:text-amber-400 font-mono">
                            {{ substr($j->jam_mulai, 0, 5) }}
                        </p>
                        <div class="flex items-center justify-center my-0.5">
                            <div class="h-px w-4 bg-slate-300 dark:bg-zinc-600"></div>
                        </div>
                        <p class="text-[10px] text-slate-400 dark:text-zinc-500 font-mono">
                            {{ substr($j->jam_selesai, 0, 5) }}
                        </p>
                    </div>

                    <div class="w-px h-10 bg-slate-200 dark:bg-zinc-700 flex-shrink-0"></div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">
                            {{ $j->mapel->nama }}
                        </p>
                        <p class="text-xs text-slate-400 dark:text-zinc-500 flex items-center gap-1 mt-0.5">
                            <i data-lucide="door-open" class="w-3 h-3 flex-shrink-0"></i>
                            Kelas {{ $j->kelas->nama }}
                        </p>
                    </div>

                    {{-- Tombol isi jurnal (hanya hari ini) --}}
                    @if($hariNum == $hariIni)
                    <a href="{{ route('guru.jurnal.create', ['jadwal_id' => $j->id]) }}"
                       class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1.5 bg-amber-400 hover:bg-amber-500 text-zinc-900 text-xs font-semibold rounded-lg transition-colors active:scale-95">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Isi</span>
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endforeach
    </div>
    @endif
</div>

{{-- Hari tanpa jadwal (info) --}}
@if($jadwal->isNotEmpty())
@php
    $hariTanpaJadwal = collect($namaHari)->filter(fn($_, $n) => !$jadwal->has($n))->values();
@endphp
@if($hariTanpaJadwal->isNotEmpty())
<div class="mt-4 flex items-center gap-2 px-4 py-3 bg-slate-50 dark:bg-zinc-800/50 rounded-xl text-xs text-slate-400 dark:text-zinc-500 border border-slate-100 dark:border-zinc-700/50">
    <i data-lucide="info" class="w-3.5 h-3.5 flex-shrink-0"></i>
    <span>Tidak ada jadwal: {{ $hariTanpaJadwal->implode(', ') }}</span>
</div>
@endif
@endif

@endsection
