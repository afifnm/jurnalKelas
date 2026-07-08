@extends('layouts.app')
@php
$roleName = auth()->user()->hasRole('admin') ? 'Admin' : 'Kepala Sekolah';
@endphp
@section('title', 'Dashboard ' . $roleName)
@section('page-title', 'Dashboard')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>{{ $roleName }}</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i><span class="text-slate-700 dark:text-zinc-200 font-medium">Dashboard</span>
@endsection

@section('content')
@php
$rolePrefix = auth()->user()->hasRole('admin') ? 'admin.' : 'ks.';
@endphp
<!-- Quick Actions -->
<div class="flex flex-wrap gap-3 mb-6">
    <a href="{{ route($rolePrefix . 'jadwal.print.semua') }}" target="_blank"
       class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors">
        <i data-lucide="printer" class="w-4 h-4"></i>
        Cetak Jadwal Pelajaran
    </a>
    @php
        $bebanMengajarRoute = auth()->user()->hasRole('admin') 
            ? route('admin.tugas-mengajar.print') 
            : route('ks.jadwal.print.beban-mengajar');
    @endphp
    <a href="{{ $bebanMengajarRoute }}" target="_blank"
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
            <a href="{{ route($rolePrefix . 'dashboard.mengajar-sekarang') }}"
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
            @foreach($mengajarSekarang->take(8) as $grup)
            @php 
                $j = $grup['jadwal']->first(); 
                $jLast = $grup['jadwal']->last(); 
                
                $jadwalGuruRoute = auth()->user()->hasRole('admin') ? 'admin.jadwal.by-guru' : (auth()->user()->hasRole('ks') ? 'ks.jadwal.by-guru' : 'guru.jadwal.index');
                $jadwalKelasRoute = auth()->user()->hasRole('admin') ? 'admin.jadwal.index' : (auth()->user()->hasRole('ks') ? 'ks.jadwal.by-kelas' : 'guru.jadwal.by-kelas');
            @endphp
            <div class="flex items-center gap-3 px-5 py-3">
                <div class="w-7 h-7 rounded-lg bg-green-100 dark:bg-green-950/40 flex items-center justify-center text-green-700 dark:text-green-400 text-xs font-bold flex-shrink-0">
                    {{ $j->guru->username }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate">
                        <a href="{{ route($jadwalGuruRoute, ['guru_id' => $j->guru->id]) }}" class="hover:underline hover:text-blue-600">{{ $j->guru->nama }}</a>
                    </p>
                    <p class="text-[10px] text-slate-400 dark:text-zinc-500 truncate">
                        {{ $j->mapel->nama }} &middot; 
                        <a href="{{ route($jadwalKelasRoute, ['kelas_id' => $j->kelas->id]) }}" class="hover:underline">{{ $j->kelas->nama }}</a>
                    </p>
                </div>
                <span class="text-[10px] font-mono text-green-600 dark:text-green-400 flex-shrink-0">
                    {{ substr($j->jamPelajaran->jam_mulai,0,5) }}&ndash;{{ substr($jLast->jamPelajaran->jam_selesai,0,5) }}
                </span>
            </div>
            @endforeach
        </div>
        @if($mengajarSekarang->count() > 8)
        <div class="px-5 py-3 border-t border-slate-100 dark:border-zinc-700/50 text-center">
            <a href="{{ route($rolePrefix . 'dashboard.mengajar-sekarang') }}"
               class="text-xs text-green-600 dark:text-green-400 hover:underline font-medium">
                +{{ $mengajarSekarang->count() - 8 }} sesi lainnya &rarr;
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
            <a href="{{ route($rolePrefix . 'dashboard.belum-isi-jurnal') }}"
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
                    {{ $guru->username }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-slate-600 dark:text-zinc-400 truncate">
                        @php
                            $jadwalGuruRoute = auth()->user()->hasRole('admin') ? 'admin.jadwal.by-guru' : (auth()->user()->hasRole('ks') ? 'ks.jadwal.by-guru' : 'guru.jadwal.index');
                        @endphp
                        <a href="{{ route($jadwalGuruRoute, ['guru_id' => $guru->id]) }}" class="hover:underline hover:text-blue-600">{{ $guru->nama }}</a>
                    </p>
                    @php $jmlJadwal = $jadwalBelumIsi->get($guru->id)?->count() ?? 0; @endphp
                    <p class="text-[10px] text-slate-400 dark:text-zinc-500">{{ $jmlJadwal }} sesi belum diisi</p>
                </div>
                <i data-lucide="clock" class="w-3.5 h-3.5 text-red-400 flex-shrink-0"></i>
            </div>
            @endforeach
        </div>
        @if($belumIsiHariIni->count() > 7)
        <div class="px-5 py-3 border-t border-slate-100 dark:border-zinc-700/50 text-center">
            <a href="{{ route($rolePrefix . 'dashboard.belum-isi-jurnal') }}"
               class="text-xs text-red-500 dark:text-red-400 hover:underline font-medium">
                +{{ $belumIsiHariIni->count() - 7 }} guru lainnya &rarr;
            </a>
        </div>
        @endif
        @endif
    </div>

</div>

<!-- Grafik Tren Kepatuhan -->
<div class="card mb-4">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-zinc-700/50">
        <div class="flex items-center gap-2">
            <i data-lucide="trending-up" class="w-4 h-4 text-green-500"></i>
            <h3 class="font-semibold text-sm text-slate-800 dark:text-white">Tren Kepatuhan Jurnal</h3>
            <span class="text-xs text-slate-400 dark:text-zinc-500">30 hari terakhir</span>
        </div>
        <div class="flex items-center gap-3 text-[10px] text-slate-400 dark:text-zinc-500">
            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span> ≥80%</span>
            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span> 50–79%</span>
            <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span> &lt;50%</span>
        </div>
    </div>
    <div class="px-2 py-3">
        <div id="chart-tren-kepatuhan" style="min-height:200px"></div>
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
            <a href="{{ route($rolePrefix . 'jurnal.index') }}" class="text-xs text-amber-600 dark:text-amber-400 hover:underline">Lihat semua</a>
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
                    {{ $j->guru->username }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $j->guru->nama }}</p>
                    <p class="text-[10px] text-slate-400">{{ $j->mapel->nama }} · {{ $j->kelas->nama }} · {{ $j->tanggal->translatedFormat('j M') }}</p>
                </div>
                @if(!$j->isInputDalamJamMengajar($jamSesiMap[$j->id] ?? null))
                <span class="text-[10px] text-red-500 dark:text-red-400 font-medium flex-shrink-0">Di luar jam</span>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    const tren = @json($trenKepatuhan);

    const labels  = tren.map(d => d.label);
    const data    = tren.map(d => d.persen);
    const isi     = tren.map(d => d.isi);
    const total   = tren.map(d => d.total);

    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#a1a1aa' : '#64748b';
    const gridColor = isDark ? '#27272a' : '#f1f5f9';

    // Warna titik per data point
    const pointColors = data.map(v => {
        if (v === null) return '#94a3b8';
        if (v >= 80)   return '#22c55e';
        if (v >= 50)   return '#f59e0b';
        return '#ef4444';
    });

    const options = {
        chart: {
            type: 'area',
            height: 200,
            toolbar: { show: false },
            zoom: { enabled: false },
            background: 'transparent',
            fontFamily: 'Plus Jakarta Sans, Inter, sans-serif',
        },
        series: [{ name: 'Kepatuhan', data: data }],
        xaxis: {
            categories: labels,
            tickAmount: 6,
            labels: { style: { colors: textColor, fontSize: '10px' }, rotate: -30 },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            min: 0,
            max: 100,
            tickAmount: 4,
            labels: {
                formatter: v => (v !== null ? v + '%' : '-'),
                style: { colors: textColor, fontSize: '10px' },
            },
        },
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.02, stops: [0, 100] },
        },
        stroke: { curve: 'smooth', width: 2, colors: ['#22c55e'] },
        colors: ['#22c55e'],
        markers: {
            size: 4,
            colors: pointColors,
            strokeColors: isDark ? '#18181b' : '#ffffff',
            strokeWidth: 1.5,
            hover: { size: 6 },
        },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            y: {
                formatter: (val, { dataPointIndex }) => {
                    const t = total[dataPointIndex];
                    const i = isi[dataPointIndex];
                    if (t === 0) return 'Tidak ada jadwal';
                    return `${i} dari ${t} guru mengisi (${val ?? 0}%)`;
                },
            },
        },
        grid: {
            borderColor: gridColor,
            strokeDashArray: 4,
            padding: { left: 4, right: 4 },
        },
        dataLabels: { enabled: false },
        theme: { mode: isDark ? 'dark' : 'light' },
        annotations: {
            yaxis: [
                { y: 80, borderColor: '#22c55e', borderWidth: 1, strokeDashArray: 4,
                  label: { text: '80%', style: { color: '#22c55e', fontSize: '9px', background: 'transparent', border: 0 } } },
            ],
        },
    };

    const chart = new ApexCharts(document.querySelector('#chart-tren-kepatuhan'), options);
    chart.render();
})();
</script>
@endpush
