@extends('layouts.app')
@section('title', 'Dashboard Kepala Sekolah')
@section('page-title', 'Dashboard')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Kepala Sekolah</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i><span class="text-slate-700 dark:text-zinc-200 font-medium">Dashboard</span>
@endsection

@section('content')
<!-- Quick Actions -->
<div class="flex flex-wrap gap-3 mb-6">
    <a href="{{ route('ks.jadwal.print.semua') }}" target="_blank"
       class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors">
        <i data-lucide="printer" class="w-4 h-4"></i>
        Cetak Jadwal Pelajaran
    </a>
    <a href="{{ route('ks.jadwal.print.beban-mengajar') }}" target="_blank"
       class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition-colors">
        <i data-lucide="file-text" class="w-4 h-4"></i>
        Cetak Pembagian Beban Mengajar
    </a>
</div>

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

<!-- Widget Row: Mengajar Sekarang & Belum Isi -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">

    <!-- Mengajar Sekarang -->
    <div class="card">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-zinc-700/50">
            <div class="flex items-center gap-2">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                </span>
                <h3 class="font-semibold text-sm text-slate-800 dark:text-white">Mengajar Hari Ini</h3>
                <span class="text-xs text-slate-400 dark:text-zinc-500">{{ now()->translatedFormat('l') }}</span>
            </div>
            <a href="{{ route('ks.dashboard.mengajar-sekarang') }}"
               class="text-xs text-green-600 dark:text-green-400 hover:underline font-medium">
                Lihat detail
            </a>
        </div>
        @if($mengajarSekarang->isEmpty())
        <div class="flex flex-col items-center justify-center py-8 text-slate-400 dark:text-zinc-600">
            <i data-lucide="coffee" class="w-8 h-8 mb-2 opacity-40"></i>
            <p class="text-xs">Tidak ada jam pelajaran saat ini</p>
        </div>
        @else
        <div class="divide-y divide-slate-100 dark:divide-zinc-700/50">
            @foreach($mengajarSekarang->take(8) as $j)
            <div class="flex items-center gap-3 px-5 py-3">
                <div class="w-7 h-7 rounded-lg bg-green-100 dark:bg-green-950/40 flex items-center justify-center text-green-700 dark:text-green-400 text-xs font-bold flex-shrink-0">
                    {{ strtoupper(substr($j->guru->nama, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $j->guru->nama }}</p>
                    <p class="text-[10px] text-slate-400 dark:text-zinc-500 truncate">{{ $j->mapel->nama }} &middot; {{ $j->kelas->nama }}</p>
                </div>
                <span class="text-[10px] font-mono text-green-600 dark:text-green-400 flex-shrink-0">
                    {{ substr($j->jamPelajaran->jam_mulai,0,5) }}&ndash;{{ substr($j->jamPelajaran->jam_selesai,0,5) }}
                </span>
            </div>
            @endforeach
        </div>
        @if($mengajarSekarang->count() > 8)
        <div class="px-5 py-3 border-t border-slate-100 dark:border-zinc-700/50 text-center">
            <a href="{{ route('ks.dashboard.mengajar-sekarang') }}"
               class="text-xs text-green-600 dark:text-green-400 hover:underline font-medium">
                +{{ $mengajarSekarang->count() - 8 }} guru lainnya &rarr;
            </a>
        </div>
        @endif
        @endif
    </div>

    <!-- Belum Isi Jurnal -->
    <div class="card">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-zinc-700/50">
            <div class="flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-500"></i>
                <h3 class="font-semibold text-sm text-slate-800 dark:text-white">Belum Isi Jurnal Hari Ini</h3>
            </div>
            @if($belumIsiHariIni->isEmpty())
            <span class="badge badge-validated">Semua isi ✓</span>
            @else
            <a href="{{ route('ks.dashboard.belum-isi-jurnal') }}"
               class="text-xs text-red-500 dark:text-red-400 hover:underline font-medium">
                Lihat detail
            </a>
            @endif
        </div>
        @if($belumIsiHariIni->isEmpty())
        <div class="flex flex-col items-center justify-center py-8 text-slate-400 dark:text-zinc-600">
            <i data-lucide="party-popper" class="w-8 h-8 mb-2 opacity-50"></i>
            <p class="text-xs">Semua guru sudah mengisi jurnal</p>
        </div>
        @else
        <div class="divide-y divide-slate-100 dark:divide-zinc-700/50">
            @foreach($belumIsiHariIni->take(7) as $guru)
            <div class="flex items-center gap-3 px-5 py-3">
                <div class="w-7 h-7 rounded-lg bg-red-100 dark:bg-red-950/40 flex items-center justify-center text-red-600 dark:text-red-400 text-xs font-bold flex-shrink-0">
                    {{ strtoupper(substr($guru->nama, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-slate-600 dark:text-zinc-400 truncate">{{ $guru->nama }}</p>
                    @php $jmlJadwal = $jadwalBelumIsi->get($guru->id)?->count() ?? 0; @endphp
                    <p class="text-[10px] text-slate-400 dark:text-zinc-500">{{ $jmlJadwal }} sesi belum diisi</p>
                </div>
                <i data-lucide="clock" class="w-3.5 h-3.5 text-red-400 flex-shrink-0"></i>
            </div>
            @endforeach
        </div>
        @if($belumIsiHariIni->count() > 7)
        <div class="px-5 py-3 border-t border-slate-100 dark:border-zinc-700/50 text-center">
            <a href="{{ route('ks.dashboard.belum-isi-jurnal') }}"
               class="text-xs text-red-500 dark:text-red-400 hover:underline font-medium">
                +{{ $belumIsiHariIni->count() - 7 }} guru lainnya &rarr;
            </a>
        </div>
        @endif
        @endif
    </div>

</div>

<!-- Row bawah: Jurnal Terbaru -->
<div class="grid grid-cols-1 gap-4">
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
