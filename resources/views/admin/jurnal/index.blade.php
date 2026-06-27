@extends('layouts.app')
@section('title', 'Jurnal Guru')
@section('page-title', 'Jurnal Guru')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Admin</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i><span class="text-slate-700 dark:text-zinc-200 font-medium">Jurnal Guru</span>
@endsection

@section('content')
<div x-data="jurnalViewManager()" x-init="init()">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">Jurnal Guru</h2>
            <p class="text-sm text-slate-400 dark:text-zinc-500">
                Semua jurnal mengajar
                @if($tahunAktif)
                    — <span class="text-amber-600 dark:text-amber-400 font-medium">{{ $tahunAktif->nama }} {{ $tahunAktif->semester }}</span>
                @endif
            </p>
        </div>
    </div>

    <!-- Filter -->
    <form method="GET" x-ref="filterForm" class="card p-4 mb-4 space-y-3">
        <input type="hidden" name="periode" x-ref="periodeInput" value="{{ request('periode') }}">

        {{-- Shortcut Periode --}}
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-semibold text-slate-500 dark:text-zinc-400 mr-1">Periode:</span>
            @foreach(['hari_ini' => 'Hari Ini', 'minggu_ini' => 'Minggu Ini', 'bulan_ini' => 'Bulan Ini'] as $val => $label)
            <button type="button"
                @click="setPeriode('{{ $val }}')"
                :class="aktivePeriode === '{{ $val }}' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white dark:bg-zinc-800 text-slate-600 dark:text-zinc-300 border-slate-200 dark:border-zinc-700 hover:border-amber-400 hover:text-amber-600'"
                class="px-3 py-1.5 text-xs font-medium rounded-lg border transition-all">
                {{ $label }}
            </button>
            @endforeach
            <span class="text-slate-300 dark:text-zinc-600 text-xs mx-1">atau rentang:</span>
            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                @change="aktivePeriode = ''"
                class="input-field py-1.5 text-sm shrink-0" style="width:auto">
            <span class="text-slate-400 dark:text-zinc-600 text-xs">–</span>
            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                @change="aktivePeriode = ''"
                class="input-field py-1.5 text-sm shrink-0" style="width:auto">
        </div>

        {{-- Filter Guru & Kelas --}}
        <div class="flex flex-wrap items-center gap-2">
            <select name="guru_id" class="input-field py-1.5 text-sm shrink-0" style="width:auto">
                <option value="">Semua Guru</option>
                @foreach($guru as $g)
                    <option value="{{ $g->id }}" @selected(request('guru_id') == $g->id)>{{ $g->nama }}</option>
                @endforeach
            </select>
            <select name="kelas_id" class="input-field py-1.5 text-sm shrink-0" style="width:auto">
                <option value="">Semua Kelas</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id }}" @selected(request('kelas_id') == $k->id)>{{ $k->nama }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary py-1.5 text-sm shrink-0">
                <i data-lucide="search" class="w-3.5 h-3.5"></i> Cari
            </button>
            @if(request()->hasAny(['guru_id','kelas_id','tanggal_dari','tanggal_sampai','periode']))
            <a href="{{ route('admin.jurnal.index') }}"
               class="inline-flex items-center gap-1 py-1.5 px-3 text-sm rounded-lg border border-slate-200 dark:border-zinc-700 text-slate-500 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors shrink-0">
                <i data-lucide="x" class="w-3.5 h-3.5"></i> Reset
            </a>
            @endif
        </div>
    </form>

    <!-- Tabel -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-zinc-800/60 border-b border-slate-200 dark:border-zinc-700/50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider w-10">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Guru</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Kelas / Mapel</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Keterlambatan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Materi</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-700/50">
                    @forelse($jurnal as $j)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="px-4 py-3.5 text-slate-400 dark:text-zinc-500 text-xs">{{ $loop->iteration + ($jurnal->currentPage() - 1) * $jurnal->perPage() }}</td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center text-amber-600 dark:text-amber-400 text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($j->guru->nama, 0, 1)) }}
                                </div>
                                <span class="font-medium text-slate-700 dark:text-slate-200">{{ $j->guru->nama }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="font-medium text-slate-700 dark:text-slate-200">{{ $j->kelas->nama }}</p>
                            <p class="text-xs text-slate-400">{{ $j->mapel->nama }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="font-semibold text-slate-700 dark:text-slate-200">{{ $j->tanggal->translatedFormat('l, j F Y') }}</p>
                            <p class="text-xs text-slate-400">{{ $j->jam_masuk_aktual ? substr($j->jam_masuk_aktual, 0, 5) : '-' }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            @if($j->is_terlambat)
                                <span class="badge bg-red-100 dark:bg-red-950/40 text-red-700 dark:text-red-400">
                                    <i data-lucide="clock-alert" class="w-3 h-3"></i> +{{ $j->menit_terlambat }} mnt
                                </span>
                            @else
                                <span class="badge badge-validated">
                                    <i data-lucide="clock-check" class="w-3 h-3"></i> Tepat waktu
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="text-slate-600 dark:text-zinc-400 line-clamp-2 text-sm max-w-48">{{ $j->materi }}</p>
                            @if($j->lampiran->count())
                            <span class="text-[10px] text-slate-400 flex items-center gap-1 mt-0.5">
                                <i data-lucide="paperclip" class="w-3 h-3"></i>{{ $j->lampiran->count() }} foto
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end">
                                <button @click="viewDetail({{ $j->id }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/30 rounded-lg transition-colors">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center">
                        <div class="flex flex-col items-center text-slate-400 dark:text-zinc-600">
                            <i data-lucide="inbox" class="w-10 h-10 mb-2 opacity-50"></i>
                            <p class="text-sm">Belum ada jurnal</p>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($jurnal->hasPages())
        <div class="px-4 py-3 border-t border-slate-100 dark:border-zinc-700/50">{{ $jurnal->links() }}</div>
        @endif
    </div>

    <!-- Modal Detail -->
    <div x-show="detailModal" x-transition.opacity
         x-effect="document.documentElement.style.overflow = detailModal ? 'hidden' : ''; document.body.style.overflow = detailModal ? 'hidden' : ''"
         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
         @click.self="detailModal = false">
        <div x-show="detailModal" x-transition.scale.95 @click.stop
             class="w-full max-w-xl bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden max-h-[90vh]">
            <div class="overflow-y-auto max-h-[90vh]">

                <div class="sticky top-0 z-10 flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-zinc-700 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-md">
                    <div class="flex items-center gap-2">
                        <i data-lucide="notebook" class="w-4 h-4 text-amber-500"></i>
                        <h3 class="font-semibold text-slate-800 dark:text-white">Detail Jurnal</h3>
                    </div>
                    <button @click="detailModal = false">
                        <i data-lucide="x" class="w-5 h-5 text-slate-400 hover:text-slate-600"></i>
                    </button>
                </div>

                <div class="p-6">
                    <template x-if="detailData">
                        <div class="space-y-5">

                            {{-- Info utama --}}
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
                                    <p class="text-xs text-slate-400 dark:text-zinc-500 mb-0.5">Jam Masuk</p>
                                    <p class="font-semibold text-slate-700 dark:text-slate-200" x-text="(detailData.jam_masuk_aktual || '-').substring(0,5)"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 dark:text-zinc-500 mb-0.5">Jam Keluar</p>
                                    <p class="font-semibold text-slate-700 dark:text-slate-200" x-text="detailData.jam_keluar_aktual ? detailData.jam_keluar_aktual.substring(0,5) : '-'"></p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-xs text-slate-400 dark:text-zinc-500 mb-0.5">Keterlambatan</p>
                                    <p class="font-semibold"
                                       :class="detailData.is_terlambat ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'"
                                       x-text="detailData.is_terlambat ? '+' + detailData.menit_terlambat + ' menit terlambat' : 'Tepat waktu'"></p>
                                </div>
                            </div>

                            {{-- Materi --}}
                            <div class="border-t border-slate-100 dark:border-zinc-700/50 pt-4">
                                <p class="text-xs text-slate-400 dark:text-zinc-500 mb-1.5">Materi Pembelajaran</p>
                                <p class="text-sm text-slate-700 dark:text-slate-200 whitespace-pre-wrap leading-relaxed" x-text="detailData.materi"></p>
                            </div>

                            {{-- Catatan --}}
                            <div x-show="detailData.catatan" class="border-t border-slate-100 dark:border-zinc-700/50 pt-4">
                                <p class="text-xs text-slate-400 dark:text-zinc-500 mb-1.5">Catatan Tambahan</p>
                                <p class="text-sm text-slate-700 dark:text-slate-200 whitespace-pre-wrap leading-relaxed" x-text="detailData.catatan"></p>
                            </div>

                            {{-- Foto Lampiran --}}
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
        aktivePeriode: '{{ request('periode', '') }}',

        init() {
            this.$nextTick(() => lucide.createIcons());
        },

        setPeriode(val) {
            if (this.aktivePeriode === val) {
                this.aktivePeriode = '';
                this.$refs.periodeInput.value = '';
            } else {
                this.aktivePeriode = val;
                this.$refs.periodeInput.value = val;
            }
            this.$refs.filterForm.submit();
        },

        formatTanggal(str) {
            if (!str) return '-';
            const hari  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            const bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            const d = new Date(str);
            return `${hari[d.getDay()]}, ${d.getDate()} ${bulan[d.getMonth()]} ${d.getFullYear()}`;
        },

        async viewDetail(id) {
            this.detailData = null;
            this.detailModal = true;
            const res = await fetch(`/admin/jurnal/${id}`, { headers: { 'Accept': 'application/json' } });
            this.detailData = await res.json();
            this.$nextTick(() => lucide.createIcons());
        },
    }
}
</script>
@endpush
