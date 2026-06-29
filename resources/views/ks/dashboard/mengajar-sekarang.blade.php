@extends('layouts.app')
@section('title', 'Mengajar Hari Ini')
@section('page-title', 'Mengajar Hari Ini')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Kepala Sekolah</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <a href="{{ route('ks.dashboard') }}" class="hover:underline">Dashboard</a>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <span class="text-slate-700 dark:text-zinc-200 font-medium">Mengajar Hari Ini</span>
@endsection

@section('content')
<div class="mb-5 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <span class="relative flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
        </span>
        <div>
            <p class="text-sm font-semibold text-slate-700 dark:text-zinc-200">
                {{ now()->translatedFormat('l, d F Y') }} &middot; {{ now()->format('H:i') }}
            </p>
            <p class="text-xs text-slate-400 dark:text-zinc-500">
                {{ $mengajarSekarang->count() }} guru mengajar hari ini
                @if($tahunAktif)
                &middot; {{ $tahunAktif->nama }} {{ $tahunAktif->semester }}
                @endif
            </p>
        </div>
    </div>
    <a href="{{ route('ks.dashboard') }}"
       class="inline-flex items-center gap-1.5 text-xs text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-200 transition-colors">
        <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
        Kembali
    </a>
</div>

@if($mengajarSekarang->isEmpty())
<div class="card flex flex-col items-center justify-center py-16 text-slate-400 dark:text-zinc-600">
    <i data-lucide="coffee" class="w-12 h-12 mb-3 opacity-40"></i>
    <p class="text-sm font-medium">Tidak ada jam pelajaran saat ini</p>
    <p class="text-xs mt-1">Coba refresh halaman beberapa saat lagi</p>
</div>
@else
<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-slate-100 dark:border-zinc-700/50 bg-slate-50 dark:bg-zinc-800/50">
                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wide w-8">No</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Guru</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Mata Pelajaran</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Kelas</th>
                <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Jam Ke</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Waktu</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-zinc-700/50">
            @foreach($mengajarSekarang as $i => $j)
            <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/20 transition-colors">
                <td class="px-5 py-3.5 text-xs text-slate-400 dark:text-zinc-500">{{ $i + 1 }}</td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-950/40 flex items-center justify-center text-green-700 dark:text-green-400 text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($j->guru->nama, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800 dark:text-white text-xs">{{ $j->guru->nama }}</p>
                            <p class="text-[10px] text-slate-400 dark:text-zinc-500">{{ $j->guru->username }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-3.5">
                    <p class="text-xs font-medium text-slate-700 dark:text-zinc-200">{{ $j->mapel->nama }}</p>
                    @if($j->mapel->kode)
                    <p class="text-[10px] text-slate-400 dark:text-zinc-500">{{ $j->mapel->kode }}</p>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-purple-50 dark:bg-purple-950/30 text-purple-700 dark:text-purple-400 text-xs font-semibold">
                        {{ $j->kelas->nama }}
                    </span>
                </td>
                <td class="px-5 py-3.5 text-center">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-400 text-xs font-bold">
                        {{ $j->jamPelajaran->jam_ke }}
                    </span>
                </td>
                <td class="px-5 py-3.5">
                    <span class="text-xs font-mono font-bold text-green-600 dark:text-green-400">
                        {{ substr($j->jamPelajaran->jam_mulai,0,5) }} &ndash; {{ substr($j->jamPelajaran->jam_selesai,0,5) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
