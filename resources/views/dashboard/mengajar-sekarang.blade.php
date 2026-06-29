@extends('layouts.app')
@section('title', 'Mengajar Hari Ini')
@section('page-title', 'Mengajar Hari Ini')
@php
$roleName = auth()->user()->hasRole('admin') ? 'Admin' : 'Kepala Sekolah';
$rolePrefix = auth()->user()->hasRole('admin') ? 'admin.' : 'ks.';
@endphp
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>{{ $roleName }}</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <a href="{{ route($rolePrefix . 'dashboard') }}" class="hover:underline">Dashboard</a>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <span class="text-slate-700 dark:text-zinc-200 font-medium">Mengajar Hari Ini</span>
@endsection

@section('content')
<div x-data="jurnalViewManager()" x-init="init()">
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
        <a href="{{ route($rolePrefix . 'dashboard') }}"
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
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wide">Status Jurnal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-zinc-700/50">
                @foreach($mengajarSekarang as $i => $grup)
                @php
                $j     = $grup['jadwal']->first();
                $jLast = $grup['jadwal']->last();
                $keAwal  = $j->jamPelajaran->jam_ke;
                $keAkhir = $jLast->jamPelajaran->jam_ke;
                $jamKeLabel = $keAwal === $keAkhir ? "Ke-{$keAwal}" : "Ke-{$keAwal}–{$keAkhir}";
                $jumlahJam = $grup['jadwal']->count();
                
                $isFilled = isset($grup['jurnal']) && $grup['jurnal'];
                $detailUrl = $isFilled ? route($rolePrefix . 'jurnal.show', $grup['jurnal']->id) : null;
                
                $jadwalGuruRoute = auth()->user()->hasRole('admin') ? 'admin.jadwal.by-guru' : (auth()->user()->hasRole('ks') ? 'ks.jadwal.by-guru' : 'guru.jadwal.index');
                $jadwalKelasRoute = auth()->user()->hasRole('admin') ? 'admin.jadwal.index' : (auth()->user()->hasRole('ks') ? 'ks.jadwal.by-kelas' : 'guru.jadwal.by-kelas');
            @endphp
            <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/20 transition-colors">
                <td class="px-5 py-3.5 text-xs text-slate-400 dark:text-zinc-500">{{ $i + 1 }}</td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-950/40 flex items-center justify-center text-green-700 dark:text-green-400 text-xs font-bold flex-shrink-0">
                            {{ $j->guru->username }}
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800 dark:text-white text-xs">
                                <a href="{{ route($jadwalGuruRoute, ['guru_id' => $j->guru->id]) }}" class="hover:underline hover:text-blue-600">{{ $j->guru->nama }}</a>
                            </p>
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
                        <a href="{{ route($jadwalKelasRoute, ['kelas_id' => $j->kelas->id]) }}" class="hover:underline">{{ $j->kelas->nama }}</a>
                    </span>
                </td>
                <td class="px-5 py-3.5 text-center">
                    <div class="flex flex-col items-center gap-0.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-400 text-xs font-bold">
                            {{ $jamKeLabel }}
                        </span>
                        @if($jumlahJam > 1)
                        <span class="text-[10px] text-slate-400 dark:text-zinc-500">{{ $jumlahJam }} JP</span>
                        @endif
                    </div>
                </td>
                <td class="px-5 py-3.5">
                    <span class="text-xs font-mono font-bold text-green-600 dark:text-green-400">
                        {{ substr($j->jamPelajaran->jam_mulai,0,5) }} &ndash; {{ substr($jLast->jamPelajaran->jam_selesai,0,5) }}
                    </span>
                </td>
                <td class="px-5 py-3.5">
                    @if($isFilled)
                        <button type="button" @click="viewDetail('{{ $detailUrl }}')" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md bg-green-600 text-white text-xs font-semibold hover:bg-green-700 transition-colors shadow-sm focus:ring focus:ring-green-300">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            Lihat Jurnal
                        </button>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 text-xs font-semibold">
                            <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                            Belum Diisi
                        </span>
                    @endif
                </td>
            </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Modal Detail -->
    <div x-show="detailModal" x-transition.opacity
         x-effect="document.documentElement.style.overflow = detailModal ? 'hidden' : ''; document.body.style.overflow = detailModal ? 'hidden' : ''"
         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;"
         @click.self="detailModal = false">
        <div x-show="detailModal" x-transition.scale.95 @click.stop
             class="w-full max-w-xl bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden max-h-[90vh]">
            <div class="overflow-y-auto max-h-[90vh]">

                <div class="sticky top-0 z-10 flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-zinc-700 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-md">
                    <div class="flex items-center gap-2">
                        <i data-lucide="notebook" class="w-4 h-4 text-amber-500"></i>
                        <h3 class="font-semibold text-slate-800 dark:text-white">Detail Jurnal</h3>
                    </div>
                    <button @click="detailModal = false"><i data-lucide="x" class="w-5 h-5 text-slate-400"></i></button>
                </div>

                <div class="p-6">
                    <template x-if="detailData">
                        <div class="space-y-5">

                            <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                                <div>
                                    <p class="text-xs text-slate-400 dark:text-zinc-500 mb-0.5">Guru</p>
                                    <p class="font-semibold text-slate-700 dark:text-slate-200" x-text="detailData.guru?.nama"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 dark:text-zinc-500 mb-0.5">Tanggal</p>
                                    <p class="font-semibold text-slate-700 dark:text-slate-200" x-text="formatTanggal(detailData.tanggal)"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 dark:text-zinc-500 mb-0.5">Kelas</p>
                                    <p class="font-semibold text-slate-700 dark:text-slate-200" x-text="detailData.kelas?.nama"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 dark:text-zinc-500 mb-0.5">Mata Pelajaran</p>
                                    <p class="font-semibold text-slate-700 dark:text-slate-200" x-text="detailData.mapel?.nama"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 dark:text-zinc-500 mb-0.5">Waktu Input</p>
                                    <p class="font-semibold text-slate-700 dark:text-slate-200" x-text="detailData.created_at ? detailData.created_at.substring(0,16).replace('T',' ') : '-'"></p>
                                </div>
                            </div>

                            <div class="border-t border-slate-100 dark:border-zinc-700/50 pt-4">
                                <p class="text-xs text-slate-400 dark:text-zinc-500 mb-1.5">Materi Pembelajaran</p>
                                <p class="text-sm text-slate-700 dark:text-slate-200 whitespace-pre-wrap leading-relaxed" x-text="detailData.materi"></p>
                            </div>

                            <div x-show="detailData.catatan" class="border-t border-slate-100 dark:border-zinc-700/50 pt-4">
                                <p class="text-xs text-slate-400 dark:text-zinc-500 mb-1.5">Catatan Tambahan</p>
                                <p class="text-sm text-slate-700 dark:text-slate-200 whitespace-pre-wrap leading-relaxed" x-text="detailData.catatan"></p>
                            </div>

                            <div x-show="detailData.lampiran && detailData.lampiran.length > 0" class="border-t border-slate-100 dark:border-zinc-700/50 pt-4">
                                <p class="text-xs text-slate-400 dark:text-zinc-500 mb-2.5 flex items-center gap-1.5">
                                    <i data-lucide="images" class="w-3.5 h-3.5"></i>
                                    Foto Dokumentasi KBM (<span x-text="detailData.lampiran?.length"></span>)
                                </p>
                                <div class="grid grid-cols-3 gap-2">
                                    <template x-for="lmp in detailData.lampiran" :key="lmp.id">
                                        <a :href="lmp.url" target="_blank"
                                           class="aspect-square rounded-xl overflow-hidden bg-slate-100 dark:bg-zinc-800 hover:opacity-90 transition-opacity block">
                                            <img :src="lmp.url" :alt="lmp.keterangan || 'Foto KBM'"
                                                 class="w-full h-full object-cover">
                                        </a>
                                    </template>
                                </div>
                            </div>

                        </div>
                    </template>

                    <div x-show="!detailData" class="flex items-center justify-center py-12">
                        <div class="flex items-center gap-2 text-slate-400">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span class="text-sm">Memuat...</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function jurnalViewManager() {
    return {
        detailModal: false,
        detailData: null,

        init() { this.$nextTick(() => lucide.createIcons()); },

        formatTanggal(str) {
            if (!str) return '-';
            const hari  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            const bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            const d = new Date(str);
            return `${hari[d.getDay()]}, ${d.getDate()} ${bulan[d.getMonth()]} ${d.getFullYear()}`;
        },

        async viewDetail(url) {
            this.detailData = null;
            this.detailModal = true;
            try {
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                this.detailData = await res.json();
                this.$nextTick(() => lucide.createIcons());
            } catch (error) {
                console.error("Gagal memuat detail:", error);
                this.detailModal = false;
            }
        },
    }
}
</script>
@endpush
