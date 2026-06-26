@extends('layouts.app')
@section('title', 'Dashboard Kepala Sekolah')
@section('page-title', 'Dashboard')

@section('content')
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
            <i data-lucide="clock" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
        </div>
        <p class="text-3xl font-bold text-slate-800 dark:text-white">{{ $jurnalPending }}</p>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Menunggu validasi</p>
    </div>
    <div class="card p-5">
        <div class="w-10 h-10 rounded-xl bg-green-50 dark:bg-green-950/40 flex items-center justify-center mb-3">
            <i data-lucide="percent" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
        </div>
        <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($avgKepatuhan, 0) }}%</p>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Avg. kepatuhan bulan ini</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
    <!-- Chart Tren -->
    <div class="lg:col-span-2 card p-5">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <i data-lucide="trending-up" class="w-4 h-4 text-amber-500"></i>
                <h3 class="font-semibold text-sm text-slate-800 dark:text-white">Tren Kepatuhan 6 Bulan Terakhir</h3>
            </div>
        </div>
        <div id="chart-tren"></div>
    </div>

    <!-- Guru Belum Isi -->
    <div class="card">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-zinc-700/50">
            <div class="flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-500"></i>
                <h3 class="font-semibold text-sm text-slate-800 dark:text-white">Belum Isi Hari Ini</h3>
            </div>
            @if($belumIsiHariIni->isEmpty())
            <span class="badge badge-validated">Semua isi ✓</span>
            @endif
        </div>
        @if($belumIsiHariIni->isEmpty())
        <div class="flex flex-col items-center justify-center py-8 text-slate-400 dark:text-zinc-600">
            <i data-lucide="party-popper" class="w-8 h-8 mb-2 opacity-50"></i>
            <p class="text-xs">Semua guru sudah mengisi jurnal</p>
        </div>
        @else
        <div class="divide-y divide-slate-100 dark:divide-zinc-700/50">
            @foreach($belumIsiHariIni as $guru)
            <div class="flex items-center gap-3 px-5 py-3">
                <div class="w-7 h-7 rounded-lg bg-red-100 dark:bg-red-950/40 flex items-center justify-center text-red-600 dark:text-red-400 text-xs font-bold flex-shrink-0">
                    {{ strtoupper(substr($guru->nama, 0, 1)) }}
                </div>
                <p class="text-sm text-slate-600 dark:text-zinc-400 truncate">{{ $guru->nama }}</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<!-- Tabel Kinerja Guru -->
<div class="card overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-zinc-700/50">
        <div class="flex items-center gap-2">
            <i data-lucide="bar-chart-3" class="w-4 h-4 text-amber-500"></i>
            <h3 class="font-semibold text-sm text-slate-800 dark:text-white">Kinerja Guru — {{ now()->translatedFormat('F Y') }}</h3>
        </div>
        <a href="{{ route('ks.jurnal.index') }}" class="btn-secondary text-xs">
            <i data-lucide="notebook-text" class="w-3.5 h-3.5"></i> Jurnal Guru
        </a>
    </div>

    <!-- Formula Info -->
    <div class="px-5 py-3 bg-amber-50/50 dark:bg-amber-950/20 border-b border-amber-100 dark:border-amber-900/30">
        <p class="text-xs text-amber-700 dark:text-amber-400">
            <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
            <strong>Formula Skor Kinerja:</strong> Kepatuhan Pengisian (50%) + Ketepatan Waktu Masuk (30%) + Rasio Tervalidasi (20%)
        </p>
    </div>

    @if($kinerjaGuru->isEmpty())
    <div class="flex flex-col items-center justify-center py-12 text-slate-400 dark:text-zinc-600">
        <i data-lucide="database" class="w-10 h-10 mb-2 opacity-50"></i>
        <p class="text-sm">Belum ada data kinerja bulan ini</p>
        <p class="text-xs mt-1">Jalankan: <code class="bg-slate-100 dark:bg-zinc-700 px-2 py-0.5 rounded">php artisan jurnal:hitung-metrik</code></p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 dark:bg-zinc-800/60 border-b border-slate-200 dark:border-zinc-700/50">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Guru</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Kepatuhan</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Terlambat</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Tervalidasi</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Skor</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-zinc-700/50">
                @foreach($kinerjaGuru as $k)
                <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-400 to-orange-400 flex items-center justify-center text-white text-sm font-bold">
                                {{ strtoupper(substr($k->guru->nama, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-700 dark:text-slate-200">{{ $k->guru->nama }}</p>
                                <p class="text-xs text-slate-400">{{ $k->total_terisi }}/{{ $k->total_jadwal }} jurnal</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <div class="flex flex-col items-center">
                            <span class="text-sm font-bold {{ $k->persen_kepatuhan >= 80 ? 'text-green-600 dark:text-green-400' : ($k->persen_kepatuhan >= 60 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">
                                {{ number_format($k->persen_kepatuhan, 0) }}%
                            </span>
                            <div class="w-16 h-1.5 bg-slate-100 dark:bg-zinc-700 rounded-full mt-1 overflow-hidden">
                                <div class="h-full rounded-full {{ $k->persen_kepatuhan >= 80 ? 'bg-green-500' : ($k->persen_kepatuhan >= 60 ? 'bg-amber-500' : 'bg-red-500') }}"
                                     style="width: {{ $k->persen_kepatuhan }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="{{ $k->total_terlambat > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }} font-semibold text-sm">
                            {{ $k->total_terlambat }}x
                        </span>
                        @if($k->total_terlambat > 0)
                        <p class="text-[10px] text-slate-400">avg {{ number_format($k->rata_keterlambatan_menit, 0) }} mnt</p>
                        @endif
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="text-slate-600 dark:text-zinc-400 font-semibold text-sm">{{ $k->total_validated }}/{{ $k->total_terisi }}</span>
                    </td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="text-lg font-bold {{ $k->skor_warn }}">{{ number_format($k->skor_kinerja, 1) }}</span>
                        <p class="text-[10px] text-slate-400">dari 100</p>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@push('scripts')
<script>
const trenData = @json($tren);
const opts = {
    series: [{ name: 'Kepatuhan (%)', data: trenData.map(t => t.kepatuhan) }],
    chart: { type: 'area', height: 200, toolbar: { show: false }, fontFamily: 'Plus Jakarta Sans, sans-serif',
        foreColor: document.documentElement.classList.contains('dark') ? '#a1a1aa' : '#64748b' },
    colors: ['#FACC15'],
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
    stroke: { curve: 'smooth', width: 2.5 },
    xaxis: { categories: trenData.map(t => t.bulan), labels: { style: { fontSize: '11px' } } },
    yaxis: { min: 0, max: 100, labels: { formatter: v => v + '%', style: { fontSize: '11px' } } },
    grid: { borderColor: document.documentElement.classList.contains('dark') ? '#3f3f46' : '#f1f5f9', strokeDashArray: 4 },
    dataLabels: { enabled: false },
    tooltip: { y: { formatter: v => v + '%' } }
};
new ApexCharts(document.getElementById('chart-tren'), opts).render();
</script>
@endpush
@endsection
