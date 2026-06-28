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
<div class="mb-5 flex items-start gap-3 px-4 py-3.5 bg-amber-50 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-800/40 rounded-xl">
    <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5"></i>
    <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">Ada {{ $belumDiisi->count() }} jadwal hari ini yang belum diisi!</p>
        <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">Segera isi jurnal mengajar Anda.</p>
    </div>
    <a href="{{ route('guru.jurnal.create') }}" class="btn-primary text-xs py-1.5 flex-shrink-0">
        <i data-lucide="notebook-pen" class="w-3.5 h-3.5"></i> Isi Sekarang
    </a>
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
                <p class="text-[10px] text-slate-400 dark:text-zinc-500">sesi/minggu</p>
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
                <p class="text-[10px] text-slate-400 dark:text-zinc-500">sesi hari ini</p>
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
            @foreach($jadwalHariIni as $j)
            @php $sudahDiisi = in_array($j->id, $sudahDiisiHariIni); @endphp
            <div class="flex items-center gap-4 px-5 py-3.5">
                <div class="text-center w-16 flex-shrink-0">
                    <p class="text-xs font-bold text-amber-600 dark:text-amber-400">{{ substr($j->jamPelajaran->jam_mulai, 0, 5) }}</p>
                    <p class="text-[10px] text-slate-400">{{ substr($j->jamPelajaran->jam_selesai, 0, 5) }}</p>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $j->mapel->nama }}</p>
                    <p class="text-xs text-slate-400">{{ $j->kelas->nama }}</p>
                </div>
                @if($sudahDiisi)
                    <span class="badge badge-validated"><i data-lucide="check" class="w-3 h-3"></i> Sudah diisi</span>
                @else
                    <a href="{{ route('guru.jurnal.create', ['jadwal_id' => $j->id]) }}"
                       class="badge bg-amber-100 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 hover:bg-amber-200 transition-colors text-xs">
                        <i data-lucide="plus" class="w-3 h-3"></i> Isi
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
                    <p class="text-xs text-slate-400">{{ $j->kelas->nama }} — {{ $j->tanggal->translatedFormat('l, j F Y') }}</p>
                </div>
                @if($j->is_terlambat)
                <span class="text-[10px] text-red-500 dark:text-red-400 font-medium flex-shrink-0">+{{ $j->menit_terlambat }} mnt</span>
                @else
                <span class="text-[10px] text-green-500 dark:text-green-400 font-medium flex-shrink-0">Tepat waktu</span>
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
        @php $jadwalHari = $jadwalMinggu->get($hariNum, collect()); @endphp
        @if($jadwalHari->isNotEmpty())

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

        {{-- Slot vertikal --}}
        @foreach($jadwalHari->sortBy(fn($j) => $j->jamPelajaran->jam_ke) as $j)
        <div class="flex items-center gap-3 px-5 py-2.5
            {{ $hariNum == $hariIni ? 'bg-amber-50/60 dark:bg-amber-950/10' : '' }}
            {{ !$loop->last ? 'border-b border-slate-100/70 dark:border-zinc-700/30' : '' }}">

            {{-- Jam --}}
            <div class="w-14 flex-shrink-0 text-center">
                <p class="text-xs font-bold text-amber-600 dark:text-amber-400 font-mono leading-none">{{ substr($j->jamPelajaran->jam_mulai, 0, 5) }}</p>
                <p class="text-[10px] text-slate-400 dark:text-zinc-500 font-mono mt-0.5">{{ substr($j->jamPelajaran->jam_selesai, 0, 5) }}</p>
            </div>

            <div class="w-px h-8 bg-slate-200 dark:bg-zinc-700 flex-shrink-0"></div>

            {{-- Mapel & kelas --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $j->mapel->nama }}</p>
                <p class="text-xs text-slate-400 dark:text-zinc-500">Kelas {{ $j->kelas->nama }}</p>
            </div>

            {{-- Tombol isi (hari ini saja) --}}
            @if($hariNum == $hariIni)
            <a href="{{ route('guru.jurnal.create', ['jadwal_id' => $j->id]) }}"
               class="flex-shrink-0 w-7 h-7 rounded-lg bg-amber-400 hover:bg-amber-500 flex items-center justify-center transition-colors active:scale-95">
                <i data-lucide="plus" class="w-4 h-4 text-zinc-900"></i>
            </a>
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
