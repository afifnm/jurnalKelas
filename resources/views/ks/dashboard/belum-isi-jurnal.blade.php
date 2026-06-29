@extends('layouts.app')
@section('title', 'Belum Isi Jurnal')
@section('page-title', 'Belum Isi Jurnal')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Kepala Sekolah</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <a href="{{ route('ks.dashboard') }}" class="hover:underline">Dashboard</a>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <span class="text-slate-700 dark:text-zinc-200 font-medium">Belum Isi Jurnal</span>
@endsection

@section('content')
<div class="mb-5 flex items-center justify-between">
    <div>
        <p class="text-sm font-semibold text-slate-700 dark:text-zinc-200">
            {{ now()->translatedFormat('l, d F Y') }}
        </p>
        <p class="text-xs text-slate-400 dark:text-zinc-500">
            {{ $belumIsiHariIni->count() }} guru belum mengisi jurnal hari ini
            @if($tahunAktif)
            &middot; {{ $tahunAktif->nama }} {{ $tahunAktif->semester }}
            @endif
        </p>
    </div>
    <a href="{{ route('ks.dashboard') }}"
       class="inline-flex items-center gap-1.5 text-xs text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-200 transition-colors">
        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
        Kembali
    </a>
</div>

@if($belumIsiHariIni->isEmpty())
<div class="card flex flex-col items-center justify-center py-16 text-slate-400 dark:text-zinc-600">
    <i data-lucide="party-popper" class="w-12 h-12 mb-3 opacity-50"></i>
    <p class="text-sm font-medium">Semua guru sudah mengisi jurnal</p>
    <p class="text-xs mt-1">Tidak ada yang tertinggal hari ini</p>
</div>
@else
<div class="space-y-3">
    @foreach($belumIsiHariIni as $i => $guru)
    @php $jadwalGuru = $jadwalBelumIsi->get($guru->id, collect())->sortBy(fn($j) => $j->jamPelajaran?->jam_ke); @endphp
    <div class="card overflow-hidden">
        {{-- Header guru --}}
        <div class="flex items-center gap-4 px-5 py-4 bg-red-50/60 dark:bg-red-950/20 border-b border-red-100 dark:border-red-900/30">
            <div class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-950/40 flex items-center justify-center text-red-600 dark:text-red-400 text-sm font-bold flex-shrink-0">
                {{ strtoupper(substr($guru->nama, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm text-slate-800 dark:text-white">{{ $guru->nama }}</p>
                <p class="text-[10px] text-slate-500 dark:text-zinc-400">{{ $guru->username }} &middot; {{ $jadwalGuru->count() }} sesi belum diisi</p>
            </div>
            <span class="text-[10px] px-2.5 py-1 rounded-full bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 font-semibold flex-shrink-0">
                Belum isi
            </span>
        </div>

        {{-- Tabel jadwal --}}
        <table class="w-full text-xs">
            <thead>
                <tr class="border-b border-slate-100 dark:border-zinc-700/50 bg-slate-50 dark:bg-zinc-800/40">
                    <th class="text-left px-5 py-2 text-[10px] font-semibold text-slate-400 dark:text-zinc-500 uppercase tracking-wide w-12">Jam ke</th>
                    <th class="text-left px-5 py-2 text-[10px] font-semibold text-slate-400 dark:text-zinc-500 uppercase tracking-wide">Mata Pelajaran</th>
                    <th class="text-left px-5 py-2 text-[10px] font-semibold text-slate-400 dark:text-zinc-500 uppercase tracking-wide">Kelas</th>
                    <th class="text-left px-5 py-2 text-[10px] font-semibold text-slate-400 dark:text-zinc-500 uppercase tracking-wide">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-zinc-700/30">
                @foreach($jadwalGuru as $j)
                <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/20 transition-colors">
                    <td class="px-5 py-2.5 text-center">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-100 dark:bg-zinc-700 text-slate-500 dark:text-zinc-400 text-[10px] font-bold">
                            {{ $j->jamPelajaran?->jam_ke ?? '-' }}
                        </span>
                    </td>
                    <td class="px-5 py-2.5">
                        <p class="font-medium text-slate-700 dark:text-zinc-200">{{ $j->mapel->nama }}</p>
                        @if($j->mapel->kode)
                        <p class="text-[10px] text-slate-400 dark:text-zinc-500">{{ $j->mapel->kode }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-2.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-purple-50 dark:bg-purple-950/30 text-purple-700 dark:text-purple-400 text-[10px] font-semibold">
                            {{ $j->kelas->nama }}
                        </span>
                    </td>
                    <td class="px-5 py-2.5 font-mono font-semibold text-slate-600 dark:text-zinc-300">
                        {{ substr($j->jamPelajaran->jam_mulai,0,5) }} &ndash; {{ substr($j->jamPelajaran->jam_selesai,0,5) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach
</div>
@endif
@endsection
