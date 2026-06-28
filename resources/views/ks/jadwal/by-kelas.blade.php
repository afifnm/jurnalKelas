@extends('layouts.app')
@section('title', 'Jadwal per Kelas')
@section('page-title', 'Jadwal per Kelas')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Kepala Sekolah</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i><span class="text-slate-700 dark:text-zinc-200 font-medium">Jadwal per Kelas</span>
@endsection

@section('content')
@php $hariIni = now()->dayOfWeekIso; @endphp

<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-lg font-bold text-slate-800 dark:text-white">Jadwal per Kelas</h2>
        <p class="text-sm text-slate-400 dark:text-zinc-500">Lihat seluruh jadwal pelajaran setiap kelas</p>
    </div>
    <a href="{{ route('ks.jadwal.by-guru') }}" class="btn-secondary text-sm">
        <i data-lucide="user" class="w-4 h-4"></i> Lihat per Guru
    </a>
</div>

<!-- Filter -->
<div class="card p-4 mb-5">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-zinc-400">
            <i data-lucide="filter" class="w-4 h-4"></i>
            <span class="font-medium">Filter:</span>
        </div>
        <select name="tahun_ajaran_id" class="input-field w-auto text-sm">
            <option value="">Semua Tahun Ajaran</option>
            @foreach($tahunAjaran as $ta)
                <option value="{{ $ta->id }}" @selected($tahunId == $ta->id)>
                    {{ $ta->nama }} - {{ $ta->semester }} {{ $ta->is_aktif ? '(Aktif)' : '' }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary text-sm">Tampilkan</button>
        @if(request()->has('tahun_ajaran_id'))
            <a href="{{ route('ks.jadwal.by-kelas') }}" class="btn-secondary text-sm">
                <i data-lucide="x" class="w-4 h-4"></i> Reset
            </a>
        @endif
        <span class="ml-auto text-xs text-slate-400 dark:text-zinc-500 flex items-center gap-1.5">
            <span class="w-3 h-3 rounded bg-amber-100 dark:bg-amber-950/50 border border-amber-300 dark:border-amber-700 inline-block"></span>
            Hari ini ({{ $namaHari[$hariIni] ?? '' }})
        </span>
    </form>
</div>

@if($kelasList->isEmpty())
<div class="card p-12 text-center text-slate-400 dark:text-zinc-600">
    <i data-lucide="school" class="w-12 h-12 mx-auto mb-3 opacity-40"></i>
    <p class="text-sm">Belum ada data kelas.</p>
</div>
@else

<div x-data="{ activeKelas: {{ $kelasList->first()->id }} }" class="space-y-5">

    <!-- Tab Pills -->
    <div class="flex flex-wrap gap-2">
        @foreach($kelasList as $kelas)
        @php $jumlahJadwal = collect($jadwalPerKelas[$kelas->id]['jadwal'] ?? [])->flatten()->count(); @endphp
        <button
            @click="activeKelas = {{ $kelas->id }}"
            :class="activeKelas === {{ $kelas->id }}
                ? 'bg-amber-400 text-zinc-900 shadow-sm'
                : 'bg-white dark:bg-zinc-800 text-slate-600 dark:text-zinc-300 border border-slate-200 dark:border-zinc-700 hover:border-amber-300 dark:hover:border-amber-700'"
            class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium transition-all">
            <i data-lucide="school" class="w-3.5 h-3.5"></i>
            {{ $kelas->nama }}
            @if($jumlahJadwal > 0)
            <span :class="activeKelas === {{ $kelas->id }} ? 'bg-zinc-900/20' : 'bg-slate-100 dark:bg-zinc-700 text-slate-500 dark:text-zinc-400'"
                  class="px-1.5 py-0.5 rounded-full text-xs font-semibold">{{ $jumlahJadwal }}</span>
            @endif
        </button>
        @endforeach
    </div>

    @foreach($kelasList as $kelas)
    @php
        $dataKelas = $jadwalPerKelas[$kelas->id] ?? ['kelas' => $kelas, 'jadwal' => collect()];
        $jadwalGrouped = $dataKelas['jadwal'];
    @endphp
    <div x-show="activeKelas === {{ $kelas->id }}" x-transition.opacity.duration.150ms>
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-zinc-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-purple-50 dark:bg-purple-950/40 flex items-center justify-center">
                        <i data-lucide="school" class="w-4 h-4 text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white">Kelas {{ $kelas->nama }}</h3>
                        <p class="text-xs text-slate-400 dark:text-zinc-500">
                            {{ collect($jadwalGrouped)->flatten()->count() }} jadwal terdaftar
                        </p>
                    </div>
                </div>
            </div>

            @if(collect($jadwalGrouped)->flatten()->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-slate-400 dark:text-zinc-600">
                <i data-lucide="calendar-x-2" class="w-10 h-10 mb-2 opacity-40"></i>
                <p class="text-sm">Belum ada jadwal untuk kelas ini</p>
            </div>
            @else
            <div class="divide-y divide-slate-100 dark:divide-zinc-700/50">
                @foreach($namaHari as $hariNum => $hariNama)
                @php $jadwalHari = $jadwalGrouped->get($hariNum, collect()); @endphp
                @if($jadwalHari->isNotEmpty())
                <div class="@if($hariNum == $hariIni) bg-amber-50/60 dark:bg-amber-950/10 @endif">
                    <div class="flex items-center gap-3 px-5 py-2.5">
                        <span class="text-xs font-bold uppercase tracking-wide w-14 flex-shrink-0
                            @if($hariNum == $hariIni) text-amber-600 dark:text-amber-400 @else text-slate-400 dark:text-zinc-500 @endif">
                            {{ $hariNama }}
                        </span>
                        @if($hariNum == $hariIni)
                        <span class="badge bg-amber-100 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 text-[10px]">
                            <i data-lucide="sun" class="w-2.5 h-2.5"></i> Hari ini
                        </span>
                        @endif
                    </div>
                    <div class="pb-2 space-y-1.5 px-5">
                        @foreach($jadwalHari->sortBy(fn($j) => $j->jamPelajaran->jam_ke) as $j)
                        <div class="flex items-center gap-3 p-3 rounded-xl
                            @if($hariNum == $hariIni) bg-white dark:bg-zinc-800/60 border border-amber-100 dark:border-amber-900/30 @else bg-slate-50 dark:bg-zinc-800/40 @endif">
                            <div class="text-center w-20 flex-shrink-0">
                                <p class="text-xs font-bold text-amber-600 dark:text-amber-400 font-mono">{{ substr($j->jamPelajaran->jam_mulai, 0, 5) }}</p>
                                <p class="text-[10px] text-slate-400 dark:text-zinc-500 font-mono">{{ substr($j->jamPelajaran->jam_selesai, 0, 5) }}</p>
                            </div>
                            <div class="w-px h-8 bg-slate-200 dark:bg-zinc-700 flex-shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $j->mapel->nama }}</p>
                                <p class="text-xs text-slate-400 dark:text-zinc-500 truncate flex items-center gap-1">
                                    <i data-lucide="user" class="w-3 h-3 flex-shrink-0"></i>
                                    {{ $j->guru->nama }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @endif
        </div>
    </div>
    @endforeach

</div>
@endif
@endsection
