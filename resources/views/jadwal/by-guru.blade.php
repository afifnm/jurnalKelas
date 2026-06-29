@php
    $isAdmin = auth()->user()->hasRole('admin');
    $isGuru  = auth()->user()->hasRole('guru');
    $isKs    = auth()->user()->hasRole('ks');

    // Route helpers
    $routeByGuru  = $isAdmin ? 'admin.jadwal.by-guru'  : ($isGuru ? 'guru.jadwal.index'   : 'ks.jadwal.by-guru');
    $routeByKelas = $isAdmin ? 'admin.jadwal.index'    : ($isGuru ? 'guru.jadwal.by-kelas' : 'ks.jadwal.by-kelas');
    $rolePrefix   = $isAdmin ? 'admin' : ($isGuru ? 'guru' : 'ks');
@endphp

@extends('layouts.app')
@section('title', 'Jadwal per Guru')
@section('page-title', 'Jadwal per Guru')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i>
    <span>{{ $isAdmin ? 'Admin' : ($isGuru ? 'Guru' : 'Kepala Sekolah') }}</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    @if($isAdmin)
        <a href="{{ route('admin.jadwal.index') }}" class="hover:text-amber-500">Jadwal</a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
    @elseif($isGuru)
        <a href="{{ route('guru.jadwal.index') }}" class="hover:text-amber-500">Jadwal</a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
    @endif
    <span class="text-slate-700 dark:text-zinc-200 font-medium">Per Guru</span>
@endsection

@section('content')
<div x-data="jadwalManager()">

<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-lg font-bold text-slate-800 dark:text-white">Jadwal per Guru</h2>
        <p class="text-sm text-slate-400 dark:text-zinc-500">
            {{ $isAdmin ? 'Kelola jadwal mengajar tiap guru' : 'Jadwal mengajar tiap guru' }}
        </p>
    </div>
    <div class="flex items-center gap-2">
        @if($isAdmin)
        <a href="{{ route('admin.jadwal.mapping') }}" class="btn-secondary text-sm !bg-purple-50 !text-purple-600 !border-purple-200 hover:!bg-purple-100 dark:!bg-purple-900/30 dark:!border-purple-800 dark:hover:!bg-purple-900/50">
            <i data-lucide="grip-horizontal" class="w-4 h-4"></i> Mapping (Drag &amp; Drop)
        </a>
        @endif
        <a href="{{ route($routeByKelas) }}" class="btn-secondary text-sm">
            <i data-lucide="layout-list" class="w-4 h-4"></i> Lihat per Kelas
        </a>
    </div>
</div>

@if($guruList->isEmpty())
<div class="card p-12 text-center text-slate-400 dark:text-zinc-600">
    <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 opacity-40"></i>
    <p class="text-sm">Belum ada data guru aktif.</p>
</div>
@else

{{-- Mobile: Guru Selector (horizontal scroll) --}}
<div class="md:hidden mb-4">
    <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 dark:text-zinc-500 mb-2">Pilih Guru</p>
    <div class="flex gap-1.5 overflow-x-auto pb-2 -mx-1 px-1">
        @foreach($guruList as $guru)
        @php
            $jumlahJadwal    = collect($jadwalPerGuru[$guru->id]['jadwal'] ?? [])->flatten()->count();
            $mengajarHariIni = isset($jadwalPerGuru[$guru->id]) && $jadwalPerGuru[$guru->id]['jadwal']->has($hariIni);
            $isActive        = $guruId == $guru->id;

            // Badge "sedang mengajar sekarang" (KS & admin): ada jam yang berlangsung saat ini
            $sedangMengajarSekarang = false;
            if (($isKs || $isAdmin) && $mengajarHariIni) {
                $jadwalHariIni = $jadwalPerGuru[$guru->id]['jadwal']->get($hariIni, collect());
                foreach ($jadwalHariIni as $jItem) {
                    if ($jItem->jamPelajaran && $jItem->jamPelajaran->jam_mulai <= $waktuIni && $jItem->jamPelajaran->jam_selesai >= $waktuIni) {
                        $sedangMengajarSekarang = true;
                        break;
                    }
                }
            }
        @endphp
        <a href="{{ route($routeByGuru, array_filter(['guru_id' => $guru->id])) }}"
           class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap flex-shrink-0 transition-all
               {{ $isActive
                   ? 'bg-amber-400 text-zinc-900 shadow-sm'
                   : 'bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 text-slate-600 dark:text-zinc-300' }}">
            @if($sedangMengajarSekarang && !$isActive)
            <span class="relative flex h-2 w-2 flex-shrink-0">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
            </span>
            @elseif($mengajarHariIni && !$isActive)
            <span class="w-1.5 h-1.5 rounded-full bg-green-500 flex-shrink-0"></span>
            @endif
            {{ $guru->nama }}
            @if($jumlahJadwal > 0)
            <span class="px-1 py-0.5 rounded-full text-[10px] font-semibold
                {{ $isActive ? 'bg-zinc-900/20 text-zinc-800' : 'bg-slate-100 dark:bg-zinc-700/60 text-slate-400' }}">
                {{ $jumlahJadwal }}
            </span>
            @endif
        </a>
        @endforeach
    </div>
</div>

<div class="flex gap-5">

    {{-- Sidebar guru (desktop) --}}
    <div class="hidden md:block w-max min-w-[10rem] max-w-[14rem] flex-shrink-0">
        <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 dark:text-zinc-500 px-1 mb-2">Daftar Guru</p>
        <div class="space-y-0.5">
        @foreach($guruList as $guru)
        @php
            $jumlahJadwal    = collect($jadwalPerGuru[$guru->id]['jadwal'] ?? [])->flatten()->count();
            $mengajarHariIni = isset($jadwalPerGuru[$guru->id]) && $jadwalPerGuru[$guru->id]['jadwal']->has($hariIni);
            $isActive        = $guruId == $guru->id;

            $sedangMengajarSekarang = false;
            if (($isKs || $isAdmin) && $mengajarHariIni) {
                $jadwalHariIni = $jadwalPerGuru[$guru->id]['jadwal']->get($hariIni, collect());
                foreach ($jadwalHariIni as $jItem) {
                    if ($jItem->jamPelajaran && $jItem->jamPelajaran->jam_mulai <= $waktuIni && $jItem->jamPelajaran->jam_selesai >= $waktuIni) {
                        $sedangMengajarSekarang = true;
                        break;
                    }
                }
            }
        @endphp
        <a href="{{ route($routeByGuru, array_filter(['guru_id' => $guru->id])) }}"
           class="flex items-center justify-between px-2.5 py-2 rounded-lg text-xs font-medium transition-all
               {{ $isActive
                   ? 'bg-amber-400 text-zinc-900 shadow-sm'
                   : 'text-slate-600 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800' }}">
            <div class="flex items-center gap-1.5 min-w-0">
                @if($sedangMengajarSekarang)
                <span class="relative flex h-2 w-2 flex-shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                </span>
                @elseif($mengajarHariIni)
                <span class="w-1.5 h-1.5 rounded-full bg-green-500 flex-shrink-0"></span>
                @else
                <span class="w-1.5 h-1.5 flex-shrink-0"></span>
                @endif
                <span class="whitespace-nowrap truncate">{{ $guru->nama }}</span>
            </div>
            @if($jumlahJadwal > 0)
            <span class="flex-shrink-0 px-1.5 py-0.5 rounded-full text-[10px] font-semibold
                {{ $isActive ? 'bg-zinc-900/20 text-zinc-800' : 'bg-slate-100 dark:bg-zinc-700/60 text-slate-400 dark:text-zinc-500' }}">
                {{ $jumlahJadwal }}
            </span>
            @endif
        </a>
        @endforeach
        </div>
    </div>

    {{-- Konten guru aktif --}}
    @php
        $dataGuru          = $jadwalPerGuru[$guruId] ?? null;
        $guruAktif         = $dataGuru['guru'] ?? $guruList->firstWhere('id', $guruId);
        $jadwalGrouped     = $dataGuru['jadwal'] ?? collect();
        $jadwalHariIniGuru = $jadwalGrouped->get($hariIni, collect());
        $totalSesi         = collect($jadwalGrouped)->flatten()->count();
        $tahunUntukForm    = $tahunId ?? $tahunAktif?->id;
    @endphp

    <div class="flex-1 min-w-0 space-y-4">

        {{-- Ringkasan statistik --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="card p-4">
                <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $totalSesi }}</p>
                <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Total JP/minggu</p>
            </div>
            <div class="card p-4">
                <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $jadwalGrouped->count() }}</p>
                <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Hari mengajar</p>
            </div>
            <div class="card p-4 {{ $jadwalHariIniGuru->isNotEmpty() ? 'border-amber-200 dark:border-amber-800/40' : '' }}">
                <p class="text-2xl font-bold {{ $jadwalHariIniGuru->isNotEmpty() ? 'text-amber-600 dark:text-amber-400' : 'text-slate-800 dark:text-white' }}">
                    {{ $jadwalHariIniGuru->count() }}
                </p>
                <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Sesi hari ini</p>
            </div>
            <div class="card p-4">
                <p class="text-2xl font-bold text-slate-800 dark:text-white">
                    {{ collect($jadwalGrouped)->flatten()->pluck('kelas_id')->unique()->count() }}
                </p>
                <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Kelas diampu</p>
            </div>
        </div>

        {{-- Tabel jadwal --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-slate-100 dark:border-zinc-700/50">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center text-amber-600 dark:text-amber-400 text-sm font-bold flex-shrink-0">
                        {{ $guruAktif?->username ?? '?' }}
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-bold text-slate-800 dark:text-white truncate">{{ $guruAktif?->nama }}</h3>
                        <p class="text-xs text-slate-400 dark:text-zinc-500">Jadwal mingguan</p>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 flex-shrink-0">
                    @if($guruAktif)
                    {{-- Laporan Jurnal (admin, guru & ks) --}}
                    @if($isAdmin || $isGuru || $isKs)
                    <button @click="openLaporan({{ $guruAktif->id }})"
                            class="btn-secondary text-xs py-1.5 text-purple-600 dark:text-purple-400 border-purple-200 dark:border-purple-800 hover:bg-purple-50 dark:hover:bg-purple-950/30">
                        <i data-lucide="file-bar-chart-2" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Laporan Jurnal</span>
                    </button>
                    @endif

                    {{-- Cetak --}}
                    @if($isAdmin)
                    <a href="{{ route('admin.jadwal.print.guru', array_filter(['guru' => $guruId, 'tahun_ajaran_id' => $tahunId])) }}"
                       target="_blank" class="btn-secondary text-xs py-1.5">
                        <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Cetak</span>
                    </a>
                    @elseif($isGuru)
                    <a href="{{ route('guru.jadwal.print.guru', array_filter(['guru' => $guruId, 'tahun_ajaran_id' => $tahunId])) }}"
                       target="_blank" class="btn-secondary text-xs py-1.5">
                        <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Cetak</span>
                    </a>
                    @elseif($isKs)
                    <a href="{{ route('ks.jadwal.print.semua', array_filter(['tahun_ajaran_id' => $tahunId])) }}"
                       target="_blank" class="btn-secondary text-xs py-1.5">
                        <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Cetak Semua</span>
                    </a>
                    @endif

                    {{-- Tambah (admin only) --}}
                    @if($isAdmin && $tahunUntukForm)
                    <button @click="openCreate({{ $guruId }}, {{ $tahunUntukForm }})"
                            class="btn-primary text-xs py-1.5">
                        <i data-lucide="calendar-plus" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Tambah</span>
                    </button>
                    @endif
                    @endif
                </div>
            </div>

            @if(collect($jadwalGrouped)->flatten()->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-slate-400 dark:text-zinc-600">
                <i data-lucide="calendar-x-2" class="w-10 h-10 mb-2 opacity-40"></i>
                <p class="text-sm mb-3">Belum ada jadwal untuk guru ini</p>
                @if($isAdmin && $guruAktif && $tahunUntukForm)
                <button @click="openCreate({{ $guruId }}, {{ $tahunUntukForm }})"
                        class="btn-primary text-xs py-1.5">
                    <i data-lucide="calendar-plus" class="w-3.5 h-3.5"></i> Tambah Jadwal Pertama
                </button>
                @endif
            </div>
            @else
            <div id="jadwal-list-container" class="divide-y divide-slate-100 dark:divide-zinc-700/50">
                @php
                    $jamIstirahatPerHari = ($jamPelajaran ?? collect())
                        ->where('is_istirahat', true)
                        ->groupBy('hari')
                        ->map(fn($items) => $items->sortBy('jam_ke')->values());
                @endphp
                @foreach($namaHari as $hariNum => $hariNama)
                @php $jadwalHari = $jadwalGrouped->get($hariNum, collect()); @endphp
                @if($jadwalHari->isNotEmpty())
                <div data-hari="{{ $hariNum }}" class="{{ $hariNum == $hariIni ? 'bg-amber-50/60 dark:bg-amber-950/10' : '' }}">
                    <div class="flex items-center gap-3 px-5 py-2.5">
                        <span class="text-xs font-bold uppercase tracking-wide w-14 flex-shrink-0
                            {{ $hariNum == $hariIni ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400 dark:text-zinc-500' }}">
                            {{ $hariNama }}
                        </span>
                    </div>
                    <div class="pb-2 space-y-1.5 px-5">
                        @php
                            $grups = $hariNum == $hariIni && $isGuru
                                ? ($jadwalPerGuru[$guruId]['grupHariIni'] ?? \App\Models\Jadwal::grupkanBerurutan($jadwalHari->sortBy(fn($j) => $j->jamPelajaran?->jam_mulai)))
                                : \App\Models\Jadwal::grupkanBerurutan($jadwalHari->sortBy(fn($j) => $j->jamPelajaran?->jam_mulai));
                            $istirahatHari = $jamIstirahatPerHari->get($hariNum, collect());
                            $prevLastKe = null;
                        @endphp
                        @foreach($grups as $grup)
                        @php
                            $j         = $grup['jadwal']->first();
                            $jLast     = $grup['jadwal']->last();
                            $jumlahJam = $grup['jadwal']->count();
                            $isBentrok = isset($konflikIds) && count(array_intersect($grup['ids'], $konflikIds)) > 0;

                            // "Sedang mengajar" untuk KS & admin
                            $sedangMengajar = ($isKs || $isAdmin) && $hariNum == $hariIni
                                && $j->jamPelajaran->jam_mulai <= $waktuIni
                                && $jLast->jamPelajaran->jam_selesai >= $waktuIni;

                            // "Sudah diisi" untuk guru
                            $sudah = $isGuru && $hariNum == $hariIni
                                && isset($sudahDiisiHariIni)
                                && count(array_intersect($grup['ids'], $sudahDiisiHariIni)) > 0;

                            // Istirahat antara grup sebelumnya dan grup ini
                            $istirahatSebelum = $prevLastKe !== null
                                ? $istirahatHari->filter(fn($ist) => $ist->jam_ke > $prevLastKe && $ist->jam_ke < $j->jamPelajaran->jam_ke)
                                : collect();
                            $prevLastKe = $jLast->jamPelajaran->jam_ke;
                        @endphp
                        @foreach($istirahatSebelum as $ist)
                        <div class="flex items-center gap-3 px-1 py-1">
                            <div class="text-center w-20 flex-shrink-0">
                                <p class="text-[10px] font-mono text-slate-400 dark:text-zinc-500">{{ substr($ist->jam_mulai, 0, 5) }}</p>
                                <p class="text-[10px] font-mono text-slate-400 dark:text-zinc-500">{{ substr($ist->jam_selesai, 0, 5) }}</p>
                            </div>
                            <div class="w-px h-5 bg-slate-200 dark:bg-zinc-700 flex-shrink-0"></div>
                            <div class="flex-1 flex items-center gap-1.5">
                                <span class="text-[10px] font-medium text-slate-400 dark:text-zinc-500 italic">Istirahat</span>
                            </div>
                        </div>
                        @endforeach
                        <div data-jadwal-id="{{ $j->id }}" class="flex items-center gap-3 p-3 rounded-xl
                            {{ $isBentrok
                                ? 'bg-orange-50 dark:bg-orange-950/20 border border-orange-200 dark:border-orange-800/40'
                                : ($sedangMengajar
                                    ? 'bg-orange-50 dark:bg-orange-950/20 border border-orange-200 dark:border-orange-800/40'
                                    : ($hariNum == $hariIni
                                        ? 'bg-white dark:bg-zinc-800/60 border border-amber-100 dark:border-amber-900/30'
                                        : 'bg-slate-50 dark:bg-zinc-800/40')) }}">
                            <div class="text-center w-20 flex-shrink-0">
                                <p class="text-xs font-bold font-mono
                                    {{ ($isBentrok || $sedangMengajar) ? 'text-orange-600 dark:text-orange-400' : 'text-amber-600 dark:text-amber-400' }}">
                                    {{ substr($j->jamPelajaran->jam_mulai, 0, 5) }}
                                </p>
                                <p class="text-[10px] text-slate-400 dark:text-zinc-500 font-mono">{{ substr($jLast->jamPelajaran->jam_selesai, 0, 5) }}</p>
                            </div>
                            <div class="w-px h-8 bg-slate-200 dark:bg-zinc-700 flex-shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $j->mapel->nama }}</p>
                                    @if($jumlahJam > 1)
                                    <span class="flex-shrink-0 text-[10px] font-semibold px-1.5 py-0.5 rounded-md bg-amber-100 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400">{{ $jumlahJam }} JP</span>
                                    @endif
                                    @if($isBentrok)
                                    <span class="flex-shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-orange-100 dark:bg-orange-950/50 text-orange-700 dark:text-orange-400">
                                        <i data-lucide="alert-triangle" class="w-2.5 h-2.5"></i> Bentrok
                                    </span>
                                    @elseif($sedangMengajar)
                                    <span class="flex-shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-orange-100 dark:bg-orange-950/50 text-orange-700 dark:text-orange-400">
                                        <span class="relative flex h-1.5 w-1.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span><span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-orange-500"></span></span>
                                        Sedang mengajar
                                    </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-400 dark:text-zinc-500 flex items-center gap-1">
                                    <i data-lucide="school" class="w-3 h-3 flex-shrink-0"></i>
                                    <a href="{{ route($routeByKelas, ['kelas_id' => $j->kelas->id]) }}" class="hover:underline hover:text-blue-600">
                                        Kelas {{ $j->kelas->nama }}
                                    </a>
                                </p>
                            </div>
                            {{-- Tombol aksi --}}
                            @if($isAdmin)
                            <div class="flex items-center gap-1 flex-shrink-0">
                                <button onclick="_jadwalOpenEdit({{ $j->toJson() }})"
                                        class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30 rounded-lg transition-colors">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </button>
                                <button onclick="_jadwalDelete({{ $j->id }}, '{{ addslashes($j->mapel?->nama ?? 'Tidak Ada') }}')"
                                        class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-colors">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                            @elseif($isGuru && $hariNum == $hariIni)
                            @if($sudah)
                            <span class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1.5 bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-400 text-xs font-semibold rounded-lg">
                                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                <span class="hidden sm:inline">Diisi</span>
                            </span>
                            @else
                            <a href="{{ route('guru.jurnal.create', ['jadwal_id' => $j->id]) }}"
                               class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1.5 bg-amber-400 hover:bg-amber-500 text-zinc-900 text-xs font-semibold rounded-lg transition-colors active:scale-95">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                <span class="hidden sm:inline">Isi</span>
                            </a>
                            @endif
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

    </div>
</div>
@endif

{{-- Laporan Jurnal Modal (admin, guru & ks) --}}
@if($isAdmin || $isGuru || $isKs)
<div x-show="laporanModal" x-transition.opacity
     x-effect="document.documentElement.style.overflow = laporanModal ? 'hidden' : ''"
     class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
     @click.self="laporanModal = false" style="display:none">
    <div x-show="laporanModal" x-transition.scale.95 @click.stop
         class="w-full max-w-sm bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-zinc-700">
            <div class="flex items-center gap-2">
                <i data-lucide="file-bar-chart-2" class="w-4 h-4 text-purple-500"></i>
                <h3 class="font-semibold text-slate-800 dark:text-white">Laporan Jurnal</h3>
            </div>
            <button @click="laporanModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-zinc-300">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wide mb-2">Shortcut Periode</p>
                <div class="flex gap-2 flex-wrap">
                    <button @click="setMingguIni()" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-300 hover:bg-purple-100 dark:hover:bg-purple-950/40 hover:text-purple-700 dark:hover:text-purple-300 transition-colors">Minggu Ini</button>
                    <button @click="setBulanIni()" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-300 hover:bg-purple-100 dark:hover:bg-purple-950/40 hover:text-purple-700 dark:hover:text-purple-300 transition-colors">Bulan Ini</button>
                    <button @click="setTahunAjaran()" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-300 hover:bg-purple-100 dark:hover:bg-purple-950/40 hover:text-purple-700 dark:hover:text-purple-300 transition-colors">Tahun Ajaran Ini</button>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-zinc-400 mb-1">Dari</label>
                    <input type="date" x-model="laporanDari" class="w-full text-xs border border-slate-200 dark:border-zinc-700 rounded-lg px-2.5 py-2 bg-white dark:bg-zinc-800 text-slate-700 dark:text-zinc-200 focus:outline-none focus:border-purple-400 dark:focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-zinc-400 mb-1">Sampai</label>
                    <input type="date" x-model="laporanSampai" class="w-full text-xs border border-slate-200 dark:border-zinc-700 rounded-lg px-2.5 py-2 bg-white dark:bg-zinc-800 text-slate-700 dark:text-zinc-200 focus:outline-none focus:border-purple-400 dark:focus:border-purple-500">
                </div>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" @click="laporanModal = false" class="btn-secondary flex-1 text-sm">Batal</button>
                <button type="button" @click="buatLaporan()" :disabled="!laporanDari || !laporanSampai"
                        class="flex-1 flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold bg-purple-600 hover:bg-purple-700 text-white disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i> Buat Laporan
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Modal Tambah/Edit (admin only) --}}
@if($isAdmin)
<div x-show="modal" x-transition.opacity
     x-effect="document.documentElement.style.overflow = modal ? 'hidden' : ''"
     class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
     @click.self="modal = false" style="display:none">
    <div x-show="modal" x-transition.scale.95 @click.stop
         class="w-full max-w-md bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-zinc-700 flex-shrink-0">
            <div class="flex items-center gap-2">
                <i data-lucide="calendar-clock" class="w-4 h-4 text-amber-500"></i>
                <h3 class="font-semibold text-slate-800 dark:text-white"
                    x-text="mode === 'create' && successCount > 0
                        ? `Tambah Jadwal (${successCount} ditambahkan)`
                        : (mode === 'create' ? 'Tambah Jadwal' : 'Edit Jadwal')"></h3>
            </div>
            <button @click="modal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-zinc-300">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form @submit.prevent="submitForm()" class="overflow-y-auto p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">Kelas</label>
                <select x-model="form.kelas_id"
                        class="w-full text-sm border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 bg-white dark:bg-zinc-800 text-slate-700 dark:text-zinc-200 focus:outline-none focus:border-amber-400 dark:focus:border-amber-500" required>
                    <option value="">Pilih Kelas</option>
                    @foreach($kelas as $k)
                    <option value="{{ $k->id }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
                <p x-show="errors.kelas_id" x-text="errors.kelas_id" class="text-xs text-red-500 mt-1"></p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">Mata Pelajaran</label>
                <select x-model="form.mapel_id"
                        class="w-full text-sm border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 bg-white dark:bg-zinc-800 text-slate-700 dark:text-zinc-200 focus:outline-none focus:border-amber-400 dark:focus:border-amber-500" required>
                    <option value="">Pilih Mata Pelajaran</option>
                    @foreach($mapel as $m)
                    <option value="{{ $m->id }}">{{ $m->nama }}</option>
                    @endforeach
                </select>
                <p x-show="errors.mapel_id" x-text="errors.mapel_id" class="text-xs text-red-500 mt-1"></p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">Hari</label>
                    <select x-model="form.hari" @change="onHariChange()"
                            class="no-search w-full text-sm border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 bg-white dark:bg-zinc-800 text-slate-700 dark:text-zinc-200 focus:outline-none focus:border-amber-400 dark:focus:border-amber-500" required>
                        <option value="">Hari</option>
                        @foreach($namaHari as $num => $nama)
                        <option value="{{ $num }}">{{ $nama }}</option>
                        @endforeach
                    </select>
                    <p x-show="errors.hari" x-text="errors.hari" class="text-xs text-red-500 mt-1"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">Jam Ke</label>
                    <select x-model="form.jam_ke" @change="onJamKeChange()"
                            class="no-search w-full text-sm border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 bg-white dark:bg-zinc-800 text-slate-700 dark:text-zinc-200 focus:outline-none focus:border-amber-400 dark:focus:border-amber-500"
                            :disabled="!form.hari || jamSlots.length === 0" required>
                        <option value="">Pilih Jam</option>
                        <template x-for="slot in jamSlots" :key="slot.id">
                            <option :value="String(slot.jam_ke)"
                                    x-text="`Jam ke-${slot.jam_ke}  (${slot.jam_mulai.substring(0,5)}–${slot.jam_selesai.substring(0,5)})`">
                            </option>
                        </template>
                    </select>
                    <p x-show="!form.hari || jamSlots.length === 0" class="text-xs text-slate-400 mt-1"
                       x-text="!form.hari ? 'Pilih hari terlebih dahulu' : 'Belum ada slot jam untuk hari ini'"></p>
                    <p x-show="errors.jam_mulai" x-text="errors.jam_mulai" class="text-xs text-red-500 mt-1"></p>
                </div>
            </div>
            <div x-show="errorMsg" class="flex items-start gap-2 p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800/40 rounded-xl" style="display:none">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5"></i>
                <p x-text="errorMsg" class="text-xs text-red-600 dark:text-red-400 leading-relaxed"></p>
            </div>
            <div x-show="warningMsg" class="flex items-start gap-2 p-3 bg-orange-50 dark:bg-orange-950/30 border border-orange-200 dark:border-orange-800/40 rounded-xl" style="display:none">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-orange-500 flex-shrink-0 mt-0.5"></i>
                <p x-text="warningMsg" class="text-xs text-orange-700 dark:text-orange-400 leading-relaxed"></p>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" @click="closeModalAndReset()" class="btn-secondary flex-1"
                        x-text="successCount > 0 && mode === 'create' ? 'Selesai' : 'Batal'"></button>
                <button type="submit" :disabled="loading" class="btn-primary flex-1 flex items-center justify-center gap-1.5">
                    <span x-text="loading ? 'Menyimpan...' : (mode === 'create' ? 'Tambah' : 'Simpan')"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif

</div>{{-- end x-data --}}
@endsection

@push('scripts')
<script>
function jadwalManager() {
    @if($isAdmin)
    const defaultForm = {
        guru_id: '', kelas_id: '', mapel_id: '', hari: '', jam_mulai: '', jam_selesai: '', jam_ke: '',
        tahun_ajaran_id: ''
    };
    const allJamSlots = (@json($jamPelajaran ?? collect())).filter(s => !s.is_istirahat);
    const kelasMap    = @json($kelas->keyBy('id')->map(fn($k) => ['nama' => $k->nama]));
    const mapelMap    = @json($mapel->keyBy('id')->map(fn($m) => ['nama' => $m->nama]));
    @endif
    const namaHariMap = @json($namaHari);
    const hariIni     = {{ $hariIni }};

    return {
        @if($isAdmin)
        modal: false, mode: 'create', loading: false, errorMsg: '', warningMsg: '', errors: {},
        form: { ...defaultForm }, editId: null,
        successCount: 0,
        jamSlots: [],
        @endif

        laporanModal: false,
        laporanGuruId: null,
        laporanDari: '',
        laporanSampai: '',

        init() {
            @if($isAdmin)
            window._jadwalOpenEdit = (item) => this.openEdit(item);
            window._jadwalDelete   = (id, nama) => this.deleteJadwal(id, nama);
            @endif
        },

        openLaporan(guruId) {
            this.laporanGuruId = guruId;
            this.setMingguIni();
            this.laporanModal = true;
        },

        setMingguIni() {
            const today = new Date();
            const day = today.getDay();
            const mon = new Date(today);
            mon.setDate(today.getDate() - (day === 0 ? 6 : day - 1));
            const sat = new Date(mon);
            sat.setDate(mon.getDate() + 5);
            this.laporanDari   = mon.toISOString().split('T')[0];
            this.laporanSampai = sat.toISOString().split('T')[0];
        },

        setBulanIni() {
            const today = new Date();
            const y = today.getFullYear();
            const m = String(today.getMonth() + 1).padStart(2, '0');
            const last = new Date(y, today.getMonth() + 1, 0).getDate();
            this.laporanDari   = `${y}-${m}-01`;
            this.laporanSampai = `${y}-${m}-${String(last).padStart(2, '0')}`;
        },

        setTahunAjaran() {
            const nama = '{{ $tahunAktif?->nama ?? "" }}';
            if (!nama || !nama.includes('/')) return;
            const [y1, y2] = nama.split('/').map(s => s.trim());
            this.laporanDari   = `${y1}-07-01`;
            this.laporanSampai = `${y2}-06-30`;
        },

        buatLaporan() {
            if (!this.laporanDari || !this.laporanSampai || !this.laporanGuruId) return;
            const params = new URLSearchParams({
                tanggal_dari:   this.laporanDari,
                tanggal_sampai: this.laporanSampai,
                @if($tahunId) tahun_ajaran_id: '{{ $tahunId }}', @endif
            });
            @if($isAdmin)
            window.open(`/admin/jadwal/print/laporan-jurnal-guru/${this.laporanGuruId}?` + params.toString(), '_blank');
            @elseif($isGuru)
            window.open(`/guru/jadwal/print/laporan-jurnal-guru/${this.laporanGuruId}?` + params.toString(), '_blank');
            @else
            window.open(`/ks/jadwal/print/laporan-jurnal-guru/${this.laporanGuruId}?` + params.toString(), '_blank');
            @endif
            this.laporanModal = false;
        },

        @if($isAdmin)
        onHariChange() {
            const hariNum  = parseInt(this.form.hari);
            this.jamSlots  = allJamSlots.filter(s => s.hari === hariNum);
            this.form.jam_ke      = '';
            this.form.jam_mulai   = '';
            this.form.jam_selesai = '';
        },

        onJamKeChange() {
            const slot = this.jamSlots.find(s => String(s.jam_ke) === String(this.form.jam_ke));
            if (slot) {
                this.form.jam_mulai        = (slot.jam_mulai   || '').substring(0, 5);
                this.form.jam_selesai      = (slot.jam_selesai || '').substring(0, 5);
                this.form.jam_pelajaran_id = String(slot.id);
            } else {
                this.form.jam_mulai        = '';
                this.form.jam_selesai      = '';
                this.form.jam_pelajaran_id = '';
            }
        },

        openCreate(guruId, tahunAjaranId) {
            this.mode     = 'create';
            this.editId   = null;
            this.errors   = {};
            this.errorMsg = '';
            this.warningMsg = '';
            const prevHari = this.form.hari;
            this.form = {
                ...defaultForm,
                guru_id:         String(guruId),
                tahun_ajaran_id: String(tahunAjaranId),
                kelas_id:  this.successCount > 0 ? this.form.kelas_id  : '',
                mapel_id:  this.successCount > 0 ? this.form.mapel_id  : '',
                hari:      this.successCount > 0 ? prevHari : '',
            };
            if (this.form.hari) {
                this.jamSlots = allJamSlots.filter(s => s.hari === parseInt(this.form.hari));
            }
            this.modal = true;
        },

        openEdit(item) {
            this.mode   = 'edit';
            this.editId = item.id;
            const hariNum = parseInt(item.jam_pelajaran?.hari);
            this.jamSlots = allJamSlots.filter(s => s.hari === hariNum);
            const slot = this.jamSlots.find(s =>
                s.jam_mulai.substring(0, 5) === (item.jam_pelajaran?.jam_mulai || '').substring(0, 5)
            );
            this.form = {
                guru_id:          String(item.guru_id),
                kelas_id:         String(item.kelas_id),
                mapel_id:         String(item.mapel_id),
                hari:             String(item.jam_pelajaran?.hari),
                jam_ke:           slot ? String(slot.jam_ke) : '',
                jam_pelajaran_id: slot ? String(slot.id) : '',
                jam_mulai:        (item.jam_pelajaran?.jam_mulai  || '').substring(0, 5),
                jam_selesai:      (item.jam_pelajaran?.jam_selesai || '').substring(0, 5),
                tahun_ajaran_id:  String(item.tahun_ajaran_id),
            };
            this.errors   = {};
            this.errorMsg = '';
            this.warningMsg = '';
            this.modal = true;
            this.$nextTick(() => {
                if (typeof lucide !== 'undefined') lucide.createIcons();
                document.querySelectorAll('.fixed.inset-0 select').forEach(el => {
                    if (el.tomselect) el.tomselect.setValue(el.value);
                });
            });
        },

        closeModalAndReset() {
            this.modal        = false;
            this.successCount = 0;
            this.warningMsg   = '';
        },

        async submitForm() {
            this.loading  = true;
            this.errors   = {};
            this.errorMsg = '';
            this.warningMsg = '';
            const url    = this.mode === 'create' ? '{{ route('admin.jadwal.store') }}' : `/admin/jadwal/${this.editId}`;
            const method = this.mode === 'create' ? 'POST' : 'PUT';
            try {
                const res  = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(this.form),
                });
                const data = await res.json();
                if (!res.ok) {
                    if (data.errors && data.errors.conflict) {
                        const msg = Array.isArray(data.errors.conflict) ? data.errors.conflict.join('\n') : data.errors.conflict[0];
                        Swal.fire({ icon: 'warning', title: 'Bentrokan Jadwal', text: msg, confirmButtonText: 'Tutup', confirmButtonColor: '#ef4444' });
                        return;
                    }
                    if (data.errors) {
                        this.errors = Object.fromEntries(Object.entries(data.errors).map(([k, v]) => [k, v[0]]));
                    } else {
                        this.errorMsg = data.message || 'Terjadi kesalahan.';
                    }
                    return;
                }

                const jadwal = data.jadwal;
                if (this.mode === 'create') {
                    this.successCount++;
                    this.insertJadwalCard(jadwal, data.warnings || []);
                    const prevHari  = this.form.hari;
                    const prevKelas = this.form.kelas_id;
                    const prevMapel = this.form.mapel_id;
                    const prevJamKe = parseInt(this.form.jam_ke || '0');
                    this.form = {
                        ...defaultForm,
                        guru_id:         this.form.guru_id,
                        tahun_ajaran_id: this.form.tahun_ajaran_id,
                        kelas_id:  prevKelas,
                        mapel_id:  prevMapel,
                        hari:      prevHari,
                    };
                    const nextSlot = this.jamSlots.find(s => s.jam_ke === prevJamKe + 1);
                    if (nextSlot) {
                        this.form.jam_ke      = String(nextSlot.jam_ke);
                        this.form.jam_mulai   = nextSlot.jam_mulai.substring(0, 5);
                        this.form.jam_selesai = nextSlot.jam_selesai.substring(0, 5);
                    }
                    this.warningMsg = data.warnings?.length > 0 ? data.warnings.join(' | ') : '';
                    Swal.fire({ icon: 'success', title: `Jadwal ke-${this.successCount} ditambahkan!`, text: data.message, timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' });
                } else {
                    this.updateJadwalCard(jadwal, data.warnings || []);
                    this.modal = false;
                    const icon = data.warnings?.length > 0 ? 'warning' : 'success';
                    Swal.fire({ icon, title: 'Berhasil!', text: data.message, timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' });
                }
            } catch {
                this.errorMsg = 'Gagal terhubung ke server.';
            } finally {
                this.loading = false;
            }
        },

        insertJadwalCard(jadwal, warnings) {
            const hariNum   = parseInt(jadwal.jam_pelajaran?.hari);
            const isBentrok = warnings.length > 0;
            const jamMulai  = (jadwal.jam_pelajaran?.jam_mulai  || '').substring(0, 5);
            const jamSelesai = (jadwal.jam_pelajaran?.jam_selesai || '').substring(0, 5);
            const isHariIni = hariNum === hariIni;
            const kelasNama = kelasMap[jadwal.kelas_id]?.nama  || jadwal.kelas?.nama  || '?';
            const mapelNama = mapelMap[jadwal.mapel_id]?.nama  || jadwal.mapel?.nama  || '?';

            const cardBg = isBentrok
                ? 'bg-orange-50 dark:bg-orange-950/20 border border-orange-200 dark:border-orange-800/40'
                : (isHariIni
                    ? 'bg-white dark:bg-zinc-800/60 border border-amber-100 dark:border-amber-900/30'
                    : 'bg-slate-50 dark:bg-zinc-800/40');
            const timeColor = isBentrok ? 'text-orange-600 dark:text-orange-400' : 'text-amber-600 dark:text-amber-400';
            const bentrokBadge = isBentrok
                ? `<span class="flex-shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-orange-100 dark:bg-orange-950/50 text-orange-700 dark:text-orange-400"><i data-lucide="alert-triangle" class="w-2.5 h-2.5"></i> Bentrok</span>`
                : '';

            const cardHTML = `
                <div data-jadwal-id="${jadwal.id}" class="flex items-center gap-3 p-3 rounded-xl ${cardBg}">
                    <div class="text-center w-20 flex-shrink-0">
                        <p class="text-xs font-bold ${timeColor} font-mono">${jamMulai}</p>
                        <p class="text-[10px] text-slate-400 dark:text-zinc-500 font-mono">${jamSelesai}</p>
                    </div>
                    <div class="w-px h-8 bg-slate-200 dark:bg-zinc-700 flex-shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5">
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">${mapelNama}</p>
                            ${bentrokBadge}
                        </div>
                        <p class="text-xs text-slate-400 dark:text-zinc-500 flex items-center gap-1">
                            <i data-lucide="school" class="w-3 h-3 flex-shrink-0"></i> Kelas ${kelasNama}
                        </p>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button onclick="_jadwalOpenEdit(${JSON.stringify(jadwal).replace(/"/g, '&quot;')})"
                                class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30 rounded-lg transition-colors">
                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                        </button>
                        <button onclick="_jadwalDelete(${jadwal.id}, '${mapelNama.replace(/'/g, "\\'")}')"
                                class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-colors">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>`;

            let hariSection = document.querySelector(`#jadwal-list-container [data-hari="${hariNum}"]`);
            if (!hariSection) {
                hariSection = this._createHariSection(hariNum, isHariIni, namaHariMap[hariNum] || `Hari ${hariNum}`);
            }

            const listContainer = hariSection.querySelector('.space-y-1\\.5');
            const template = document.createElement('template');
            template.innerHTML = cardHTML.trim();
            const el = template.content.firstChild;

            const existingCards = [...listContainer.querySelectorAll('[data-jadwal-id]')];
            let inserted = false;
            for (const card of existingCards) {
                const cardJam = card.querySelector('.font-mono')?.textContent?.trim();
                if (cardJam && cardJam > jamMulai) { listContainer.insertBefore(el, card); inserted = true; break; }
            }
            if (!inserted) listContainer.appendChild(el);

            if (typeof lucide !== 'undefined') lucide.createIcons();

            setTimeout(() => {
                const newCard = listContainer.querySelector(`[data-jadwal-id="${jadwal.id}"]`);
                if (newCard) {
                    newCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    newCard.classList.add('ring-2', 'ring-amber-400', 'ring-offset-1');
                    setTimeout(() => newCard.classList.remove('ring-2', 'ring-amber-400', 'ring-offset-1'), 2000);
                }
            }, 50);
        },

        updateJadwalCard(jadwal, warnings) {
            const existingCard = document.querySelector(`[data-jadwal-id="${jadwal.id}"]`);
            if (!existingCard) { location.reload(); return; }

            const oldHari = parseInt(existingCard.closest('[data-hari]')?.dataset?.hari);
            const newHari = parseInt(jadwal.jam_pelajaran?.hari);
            if (oldHari !== newHari) { existingCard.remove(); this.insertJadwalCard(jadwal, warnings); return; }

            const jamMulai   = (jadwal.jam_pelajaran?.jam_mulai  || '').substring(0, 5);
            const jamSelesai = (jadwal.jam_pelajaran?.jam_selesai || '').substring(0, 5);
            const mapelNama  = mapelMap[jadwal.mapel_id]?.nama || jadwal.mapel?.nama || '?';
            const kelasNama  = kelasMap[jadwal.kelas_id]?.nama || jadwal.kelas?.nama || '?';

            const monoEls = existingCard.querySelectorAll('.font-mono');
            if (monoEls[0]) monoEls[0].textContent = jamMulai;
            if (monoEls[1]) monoEls[1].textContent = jamSelesai;
            const mapelEl = existingCard.querySelector('.text-sm.font-semibold');
            if (mapelEl) mapelEl.textContent = mapelNama;
            const kelasEl = existingCard.querySelector('.text-xs.text-slate-400');
            if (kelasEl) kelasEl.innerHTML = `<i data-lucide="school" class="w-3 h-3 flex-shrink-0"></i> Kelas ${kelasNama}`;

            const btns = existingCard.querySelectorAll('button');
            if (btns[0]) btns[0].setAttribute('onclick', `_jadwalOpenEdit(${JSON.stringify(jadwal).replace(/"/g, '&quot;')})`);
            if (btns[1]) btns[1].setAttribute('onclick', `_jadwalDelete(${jadwal.id}, '${mapelNama.replace(/'/g, "\\'")}')`);

            if (typeof lucide !== 'undefined') lucide.createIcons();
            existingCard.classList.add('ring-2', 'ring-amber-400', 'ring-offset-1');
            setTimeout(() => existingCard.classList.remove('ring-2', 'ring-amber-400', 'ring-offset-1'), 2000);
        },

        async deleteJadwal(id, nama) {
            const { isConfirmed } = await Swal.fire({
                title: 'Hapus Jadwal?',
                text: `Jadwal "${nama}" akan dihapus.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ef4444',
            });
            if (!isConfirmed) return;
            const res  = await fetch(`/admin/jadwal/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            });
            const data = await res.json();
            const card = document.querySelector(`[data-jadwal-id="${id}"]`);
            if (card) {
                const hariSection = card.closest('[data-hari]');
                card.remove();
                if (hariSection && !hariSection.querySelector('[data-jadwal-id]')) hariSection.remove();
            }
            Swal.fire({ icon: 'success', title: 'Dihapus!', text: data.message, timer: 1200, showConfirmButton: false, toast: true, position: 'top-end' });
        },

        _createHariSection(hariNum, isHariIni, hariNama) {
            const bgClass   = isHariIni ? 'bg-amber-50/60 dark:bg-amber-950/10' : '';
            const textClass = isHariIni ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400 dark:text-zinc-500';
            const html = `
                <div data-hari="${hariNum}" class="${bgClass}">
                    <div class="flex items-center gap-3 px-5 py-2.5">
                        <span class="text-xs font-bold uppercase tracking-wide w-14 flex-shrink-0 ${textClass}">${hariNama}</span>
                    </div>
                    <div class="pb-2 space-y-1.5 px-5"></div>
                </div>`;
            const template = document.createElement('template');
            template.innerHTML = html.trim();
            const el = template.content.firstChild;
            const container = document.getElementById('jadwal-list-container');
            const allSections = [...container.querySelectorAll('[data-hari]')];
            let insertBefore = null;
            for (const sec of allSections) {
                if (parseInt(sec.dataset.hari) > hariNum) { insertBefore = sec; break; }
            }
            insertBefore ? container.insertBefore(el, insertBefore) : container.appendChild(el);
            if (typeof lucide !== 'undefined') lucide.createIcons();
            return el;
        },
        @endif
    };
}
</script>
@endpush
