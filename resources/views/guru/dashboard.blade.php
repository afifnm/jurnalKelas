@extends('layouts.app')
@section('title', 'Dashboard Guru')
@section('page-title', 'Dashboard')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Guru</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i><span class="text-slate-700 dark:text-zinc-200 font-medium">Dashboard</span>
@endsection

@section('content')

{{-- Alert belum isi --}}
@if($belumDiisi->isNotEmpty())
<div class="mb-6 rounded-2xl border-4 border-red-500 bg-red-50 dark:bg-red-950/40 overflow-hidden shadow-lg">
    <div class="flex items-center gap-3 px-5 py-4 bg-red-500 text-white">
        <i data-lucide="alert-triangle" class="w-8 h-8 animate-pulse"></i>
        <p class="text-lg md:text-xl font-bold">PERHATIAN: Anda Belum Mengisi Jurnal Hari Ini!</p>
    </div>
    <div class="px-5 py-4">
        <p class="text-base md:text-lg text-red-800 dark:text-red-300 mb-4 font-medium">
            Ada <strong>{{ $belumDiisi->count() }} kelas</strong> yang sudah Anda ajar tapi belum dicatat di jurnal:
        </p>
        <ul class="mb-5 space-y-3">
            @foreach($belumDiisi->take(3) as $grup)
            @php $first = $grup['jadwal']->first(); $last = $grup['jadwal']->last(); @endphp
            <li class="text-sm md:text-base font-semibold text-red-800 dark:text-red-300 flex flex-col md:flex-row md:items-center gap-2 bg-red-100 dark:bg-red-900/50 p-3 rounded-xl border border-red-200 dark:border-red-800">
                <div class="flex items-center gap-2">
                    <i data-lucide="clock" class="w-5 h-5 flex-shrink-0 text-red-600 dark:text-red-400"></i>
                    <span class="whitespace-nowrap">{{ substr($first->jamPelajaran->jam_mulai,0,5) }}–{{ substr($last->jamPelajaran->jam_selesai,0,5) }}</span>
                </div>
                <div class="flex-1">
                    <span class="hidden md:inline"> | </span>
                    {{ $first->mapel->nama }} <span class="hidden md:inline"> | </span>
                    <span class="block md:inline font-bold">Kelas {{ $first->kelas->nama }}</span>
                </div>
                @if($grup['jadwal']->count() > 1)
                <span class="px-2.5 py-1 bg-red-200 dark:bg-red-800/80 rounded-lg text-xs font-bold whitespace-nowrap self-start md:self-auto">({{ $grup['jadwal']->count() }} Jam)</span>
                @endif
            </li>
            @endforeach
            @if($belumDiisi->count() > 3)
            <li class="text-base font-bold text-red-600 dark:text-red-500 text-center p-2">+ {{ $belumDiisi->count() - 3 }} kelas lainnya belum diisi</li>
            @endif
        </ul>
        <a href="{{ route('guru.jurnal.create') }}"
           class="flex items-center justify-center gap-3 w-full py-4 md:py-5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-black text-lg md:text-xl transition-all active:scale-95 shadow-lg border-b-4 border-red-800">
            <i data-lucide="pointer" class="w-7 h-7 md:w-8 md:h-8 animate-bounce"></i>
            👉 KLIK DI SINI UNTUK MENGISI JURNAL SEKARANG 👈
        </a>
    </div>
</div>
@endif

{{-- Compact stat strip --}}
<div class="card px-4 py-3 mb-5">
    <div class="flex items-center flex-wrap gap-x-4 gap-y-2">

        <div class="flex items-center gap-2 min-w-0">
            <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center flex-shrink-0">
                <i data-lucide="calendar-range" class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400"></i>
            </div>
            <div class="leading-tight">
                <p class="text-base font-bold text-slate-800 dark:text-white">{{ $totalSesi }}</p>
                <p class="text-[10px] text-slate-400 dark:text-zinc-500">JP/minggu</p>
            </div>
        </div>

        <div class="w-px h-8 bg-slate-100 dark:bg-zinc-700/60 hidden sm:block"></div>

        <div class="flex items-center gap-2 min-w-0">
            <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center flex-shrink-0">
                <i data-lucide="clock" class="w-3.5 h-3.5 {{ $jadwalHariIni->isNotEmpty() ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400 dark:text-zinc-500' }}"></i>
            </div>
            <div class="leading-tight">
                <p class="text-base font-bold {{ $jadwalHariIni->isNotEmpty() ? 'text-amber-600 dark:text-amber-400' : 'text-slate-800 dark:text-white' }}">
                    {{ $jadwalHariIni->count() }}
                </p>
                <p class="text-[10px] text-slate-400 dark:text-zinc-500">JP hari ini</p>
            </div>
        </div>

        <div class="w-px h-8 bg-slate-100 dark:bg-zinc-700/60 hidden sm:block"></div>

        <div class="flex items-center gap-2 min-w-0">
            <div class="w-7 h-7 rounded-lg bg-green-50 dark:bg-green-950/40 flex items-center justify-center flex-shrink-0">
                <i data-lucide="notebook-pen" class="w-3.5 h-3.5 text-green-600 dark:text-green-400"></i>
            </div>
            <div class="leading-tight">
                <p class="text-base font-bold text-slate-800 dark:text-white">{{ $jurnalBulanIni }}</p>
                <p class="text-[10px] text-slate-400 dark:text-zinc-500">jurnal bulan ini</p>
            </div>
        </div>

        <div class="w-px h-8 bg-slate-100 dark:bg-zinc-700/60 hidden sm:block"></div>

        <div class="flex items-center gap-2 min-w-0">
            <div class="w-7 h-7 rounded-lg bg-red-50 dark:bg-red-950/40 flex items-center justify-center flex-shrink-0">
                <i data-lucide="clock-alert" class="w-3.5 h-3.5 text-red-500 dark:text-red-400"></i>
            </div>
            <div class="leading-tight">
                <p class="text-base font-bold {{ $jurnalTerlambatBulanIni > 0 ? 'text-red-500 dark:text-red-400' : 'text-slate-800 dark:text-white' }}">
                    {{ $jurnalTerlambatBulanIni }}
                </p>
                <p class="text-[10px] text-slate-400 dark:text-zinc-500">terlambat bulan ini</p>
            </div>
        </div>

    </div>
</div>

{{-- Jadwal Hari Ini + Riwayat Jurnal --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">

    {{-- Jadwal Hari Ini --}}
    <div class="card">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-zinc-700/50">
            <div class="flex items-center gap-2">
                <i data-lucide="calendar-days" class="w-4 h-4 text-amber-500"></i>
                <h3 class="font-semibold text-sm text-slate-800 dark:text-white">Jadwal Hari Ini</h3>
                <span class="text-xs text-slate-400">({{ now()->translatedFormat('l') }})</span>
            </div>
        </div>
        @if($jadwalHariIni->isEmpty())
        <div class="flex flex-col items-center justify-center py-10 text-slate-400 dark:text-zinc-600">
            <i data-lucide="coffee" class="w-10 h-10 mb-2 opacity-50"></i>
            <p class="text-sm">Tidak ada jadwal mengajar hari ini</p>
        </div>
        @else
        <div class="divide-y divide-slate-100 dark:divide-zinc-700/50">
            @foreach($grupJadwalHariIni as $grup)
            @php
                $j         = $grup['jadwal']->first();
                $jLast     = $grup['jadwal']->last();
                $jumlahJam = $grup['jadwal']->count();
                $sudahDiisi = count(array_intersect($grup['ids'], $sudahDiisiHariIni)) > 0;
            @endphp
            <div class="flex items-center gap-4 px-5 py-3.5">
                <div class="text-center w-16 flex-shrink-0">
                    <p class="text-xs font-bold text-amber-600 dark:text-amber-400">{{ substr($j->jamPelajaran->jam_mulai, 0, 5) }}</p>
                    <p class="text-[10px] text-slate-400">{{ substr($jLast->jamPelajaran->jam_selesai, 0, 5) }}</p>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-1.5">
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $j->mapel->nama }}</p>
                        @if($jumlahJam > 1)
                        <span class="flex-shrink-0 text-[10px] font-semibold px-1.5 py-0.5 rounded-md bg-amber-100 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400">{{ $jumlahJam }} JP</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-400">
                        <a href="{{ route('guru.jadwal.by-kelas', ['kelas_id' => $j->kelas->id]) }}" class="hover:underline hover:text-blue-600">
                            {{ $j->kelas->nama }}
                        </a>
                    </p>
                </div>
                @if($sudahDiisi)
                    <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-400 text-sm font-bold flex-shrink-0 border border-green-200 dark:border-green-800">
                        <i data-lucide="check-circle-2" class="w-5 h-5"></i> Sudah Diisi
                    </span>
                @else
                    <a href="{{ route('guru.jurnal.create', ['jadwal_id' => $j->id]) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-500 text-zinc-900 font-black text-sm transition-all active:scale-95 flex-shrink-0 shadow-md border-b-2 border-amber-600">
                        <i data-lucide="pencil" class="w-5 h-5"></i> ISI JURNAL
                    </a>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Riwayat Jurnal Terbaru --}}
    <div class="card">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-zinc-700/50">
            <div class="flex items-center gap-2">
                <i data-lucide="history" class="w-4 h-4 text-amber-500"></i>
                <h3 class="font-semibold text-sm text-slate-800 dark:text-white">Jurnal Terbaru</h3>
            </div>
            <a href="{{ route('guru.jurnal.index') }}" class="text-xs text-amber-600 dark:text-amber-400 hover:underline">Lihat semua</a>
        </div>
        @if($riwayatJurnal->isEmpty())
        <div class="flex flex-col items-center justify-center py-8 text-slate-400 dark:text-zinc-600">
            <i data-lucide="notebook" class="w-8 h-8 mb-2 opacity-50"></i>
            <p class="text-xs">Belum ada jurnal</p>
        </div>
        @else
        <div class="divide-y divide-slate-100 dark:divide-zinc-700/50">
            @foreach($riwayatJurnal as $j)
            <div class="flex items-center gap-3 px-5 py-3">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-200 truncate">{{ $j->mapel->nama }}</p>
                    <p class="text-xs text-slate-400">
                        <a href="{{ route('guru.jadwal.by-kelas', ['kelas_id' => $j->kelas->id]) }}" class="hover:underline hover:text-blue-600">{{ $j->kelas->nama }}</a> — {{ $j->tanggal->translatedFormat('l, j F Y') }}
                    </p>
                </div>
                @if($j->isInputDalamJamMengajar())
                <span class="text-[10px] text-green-500 dark:text-green-400 font-medium flex-shrink-0">Dalam jam</span>
                @else
                <span class="text-[10px] text-red-500 dark:text-red-400 font-medium flex-shrink-0">Di luar jam</span>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- Jadwal Mengajar Mingguan --}}
<div class="card overflow-hidden">
    <div class="flex items-center gap-2 px-5 py-4 border-b border-slate-100 dark:border-zinc-700/50">
        <i data-lucide="calendar-range" class="w-4 h-4 text-amber-500"></i>
        <h3 class="font-semibold text-sm text-slate-800 dark:text-white">Jadwal Mengajar Mingguan</h3>
        @if($tahunAktif)
        <span class="ml-auto text-[10px] text-slate-400 dark:text-zinc-500">{{ $tahunAktif->nama }} {{ $tahunAktif->semester }}</span>
        @endif
    </div>

    @if($jadwalMinggu->isEmpty())
    <div class="flex flex-col items-center justify-center py-10 text-slate-400 dark:text-zinc-600">
        <i data-lucide="calendar-x-2" class="w-10 h-10 mb-2 opacity-40"></i>
        <p class="text-sm">Belum ada jadwal mengajar</p>
        <p class="text-xs mt-1">Hubungi admin untuk mengatur jadwal</p>
    </div>
    @else
    <div>
        @foreach($namaHari as $hariNum => $hariNama)
        @php $grups = $grupJadwalMinggu->get($hariNum, collect()); @endphp
        @if($grups->isNotEmpty())

        {{-- Label hari --}}
        <div class="flex items-center gap-2 px-5 pt-3 pb-1.5
            {{ $hariNum == $hariIni ? 'bg-amber-50/60 dark:bg-amber-950/10' : '' }}">
            <span class="text-[10px] font-bold uppercase tracking-widest
                {{ $hariNum == $hariIni ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400 dark:text-zinc-500' }}">
                {{ $hariNama }}
            </span>
            @if($hariNum == $hariIni)
            <span class="px-1.5 py-0.5 bg-amber-100 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 text-[9px] font-bold uppercase rounded-full">
                Hari ini
            </span>
            @endif
            <div class="flex-1 h-px bg-slate-100 dark:bg-zinc-700/50 ml-1"></div>
        </div>

        {{-- Slot per grup --}}
        @foreach($grups as $grup)
        @php
            $j         = $grup['jadwal']->first();
            $jLast     = $grup['jadwal']->last();
            $jumlahJam = $grup['jadwal']->count();
            $sudahIsi  = $hariNum == $hariIni && count(array_intersect($grup['ids'], $sudahDiisiHariIni)) > 0;
            $belumIsi  = $hariNum == $hariIni && !$sudahIsi;
        @endphp
        <div class="flex items-center gap-3 px-5 py-2.5
            {{ $hariNum == $hariIni ? 'bg-amber-50/60 dark:bg-amber-950/10' : '' }}
            {{ !$loop->last ? 'border-b border-slate-100/70 dark:border-zinc-700/30' : '' }}">

            {{-- Jam --}}
            <div class="w-14 flex-shrink-0 text-center">
                <p class="text-xs font-bold text-amber-600 dark:text-amber-400 font-mono leading-none">{{ substr($j->jamPelajaran->jam_mulai, 0, 5) }}</p>
                <p class="text-[10px] text-slate-400 dark:text-zinc-500 font-mono mt-0.5">{{ substr($jLast->jamPelajaran->jam_selesai, 0, 5) }}</p>
            </div>

            <div class="w-px h-8 bg-slate-200 dark:bg-zinc-700 flex-shrink-0"></div>

            {{-- Mapel & kelas --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-1.5">
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $j->mapel->nama }}</p>
                    @if($jumlahJam > 1)
                    <span class="flex-shrink-0 text-[10px] font-semibold px-1.5 py-0.5 rounded-md bg-amber-100 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400">{{ $jumlahJam }} JP</span>
                    @endif
                </div>
                <p class="text-xs text-slate-400 dark:text-zinc-500">Kelas {{ $j->kelas->nama }}</p>
            </div>

            {{-- Label status (hari ini saja) --}}
            @if($sudahIsi)
            <span class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-400 text-xs font-bold border border-green-200 dark:border-green-800">
                <i data-lucide="check-circle-2" class="w-4 h-4"></i> Sudah Diisi
            </span>
            @elseif($belumIsi)
            <span class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-100 dark:bg-red-950/40 text-red-700 dark:text-red-400 text-xs font-bold border border-red-200 dark:border-red-800 animate-pulse">
                <i data-lucide="alert-triangle" class="w-4 h-4"></i> BELUM DIISI
            </span>
            @endif

        </div>
        @endforeach

        {{-- Jarak antar hari --}}
        <div class="h-1 {{ $hariNum == $hariIni ? 'bg-amber-50/60 dark:bg-amber-950/10' : '' }}"></div>

        @endif
        @endforeach
    </div>
    @endif
</div>

@endsection
