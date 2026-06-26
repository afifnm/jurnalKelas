@extends('layouts.app')
@section('title', 'Dashboard Guru')
@section('page-title', 'Dashboard')

@section('content')
<!-- Alert belum isi -->
@if($belumDiisi->isNotEmpty())
<div class="mb-5 flex items-start gap-3 px-4 py-3.5 bg-amber-50 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-800/40 rounded-xl">
    <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5"></i>
    <div>
        <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">Ada {{ $belumDiisi->count() }} jadwal hari ini yang belum diisi!</p>
        <p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">Segera isi jurnal mengajar Anda.</p>
    </div>
    <a href="{{ route('guru.jurnal.index') }}" class="ml-auto flex-shrink-0 btn-primary text-xs py-1.5">
        <i data-lucide="notebook-pen" class="w-3.5 h-3.5"></i> Isi Sekarang
    </a>
</div>
@endif

<!-- Stat Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center">
                <i data-lucide="calendar-clock" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $jadwalHariIni->count() }}</p>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Jadwal hari ini</p>
    </div>
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-950/40 flex items-center justify-center">
                <i data-lucide="file-edit" class="w-5 h-5 text-orange-600 dark:text-orange-400"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $jurnalDraft }}</p>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Draft belum disubmit</p>
    </div>
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-950/40 flex items-center justify-center">
                <i data-lucide="rotate-ccw" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $jurnalRevisi }}</p>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Perlu revisi</p>
    </div>
    <div class="card p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-green-50 dark:bg-green-950/40 flex items-center justify-center">
                <i data-lucide="trending-up" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800 dark:text-white">
            {{ $kinerja ? number_format($kinerja->persen_kepatuhan, 0) . '%' : '-' }}
        </p>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Kepatuhan bulan ini</p>
    </div>
</div>

<!-- Jadwal Hari Ini + Riwayat -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <!-- Jadwal Hari Ini -->
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
                    <p class="text-xs font-bold text-amber-600 dark:text-amber-400">{{ substr($j->jam_mulai, 0, 5) }}</p>
                    <p class="text-[10px] text-slate-400">{{ substr($j->jam_selesai, 0, 5) }}</p>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $j->mapel->nama }}</p>
                    <p class="text-xs text-slate-400">{{ $j->kelas->nama }}</p>
                </div>
                @if($sudahDiisi)
                    <span class="badge badge-validated"><i data-lucide="check" class="w-3 h-3"></i> Sudah diisi</span>
                @else
                    <a href="{{ route('guru.jurnal.index') }}?jadwal_id={{ $j->id }}"
                       class="badge bg-amber-100 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 hover:bg-amber-200 transition-colors text-xs">
                        <i data-lucide="plus" class="w-3 h-3"></i> Isi
                    </a>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Riwayat Terbaru + Skor Kinerja -->
    <div class="flex flex-col gap-4">
        @if($kinerja)
        <div class="card p-5">
            <div class="flex items-center gap-2 mb-3">
                <i data-lucide="bar-chart-3" class="w-4 h-4 text-amber-500"></i>
                <h3 class="font-semibold text-sm text-slate-800 dark:text-white">Kinerja Bulan Ini</h3>
                <span class="text-xs text-slate-400 dark:text-zinc-500">{{ now()->translatedFormat('F Y') }}</span>
            </div>
            <div class="flex items-center justify-between mb-3">
                <div class="text-center">
                    <p class="text-2xl font-bold {{ $kinerja->skor_warn }}">{{ number_format($kinerja->skor_kinerja, 1) }}</p>
                    <p class="text-xs text-slate-400">Skor Kinerja</p>
                </div>
                <div class="text-center">
                    <p class="text-lg font-bold text-slate-700 dark:text-slate-200">{{ number_format($kinerja->persen_kepatuhan, 0) }}%</p>
                    <p class="text-xs text-slate-400">Kepatuhan</p>
                </div>
                <div class="text-center">
                    <p class="text-lg font-bold {{ $kinerja->total_terlambat > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">{{ $kinerja->total_terlambat }}</p>
                    <p class="text-xs text-slate-400">Terlambat</p>
                </div>
            </div>
            <div class="pt-2 border-t border-slate-100 dark:border-zinc-700/50">
                <p class="text-[10px] text-slate-400 dark:text-zinc-500">Formula: Kepatuhan×50% + Ketepatan Waktu×30% + Validasi×20%</p>
            </div>
        </div>
        @endif

        <div class="card flex-1">
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
                    <span class="badge {{ $j->badge_color }}">{{ $j->status_label }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
