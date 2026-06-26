@extends('layouts.app')
@section('title', 'Jurnal Guru')
@section('page-title', 'Jurnal Guru')

@section('content')
<div x-data="jurnalViewManager()" x-init="init()">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">Jurnal Guru</h2>
            <p class="text-sm text-slate-400 dark:text-zinc-500">Lihat semua jurnal mengajar yang telah diisi oleh guru</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="card p-4 mb-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <select name="guru_id" class="input-field w-auto text-sm">
                <option value="">Semua Guru</option>
                @foreach($guru as $g)
                    <option value="{{ $g->id }}" @selected(request('guru_id') == $g->id)>{{ $g->nama }}</option>
                @endforeach
            </select>
            <select name="kelas_id" class="input-field w-auto text-sm">
                <option value="">Semua Kelas</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id }}" @selected(request('kelas_id') == $k->id)>{{ $k->nama }}</option>
                @endforeach
            </select>
            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="input-field w-auto text-sm" placeholder="Dari">
            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="input-field w-auto text-sm" placeholder="Sampai">
            <button type="submit" class="btn-primary text-sm"><i data-lucide="filter" class="w-4 h-4"></i> Filter</button>
            @if(request()->hasAny(['guru_id','kelas_id','tanggal_dari','tanggal_sampai']))
            <a href="{{ route(request()->route()->getName() === 'ks.jurnal.index' ? 'ks.jurnal.index' : 'admin.jurnal.index') }}" class="btn-secondary text-sm"><i data-lucide="x" class="w-4 h-4"></i> Reset</a>
            @endif
        </form>
    </div>

    <!-- Tabel -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-zinc-800/60 border-b border-slate-200 dark:border-zinc-700/50">
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
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center text-amber-600 dark:text-amber-400 text-xs font-bold">
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
                                    <i data-lucide="clock-alert" class="w-3 h-3"></i>
                                    +{{ $j->menit_terlambat }} mnt
                                </span>
                            @else
                                <span class="badge badge-validated">
                                    <i data-lucide="clock-check" class="w-3 h-3"></i>
                                    Tepat waktu
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="text-slate-600 dark:text-zinc-400 line-clamp-2 text-sm max-w-48">{{ $j->materi }}</p>
                            @if($j->lampiran->count())
                            <span class="text-[10px] text-slate-400 flex items-center gap-1 mt-0.5">
                                <i data-lucide="paperclip" class="w-3 h-3"></i>{{ $j->lampiran->count() }} lampiran
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                <button @click="viewDetail({{ $j->id }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/30 rounded-lg transition-colors">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center">
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
    <div x-show="detailModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="detailModal = false">
        <div x-show="detailModal" x-transition.scale.95 class="w-full max-w-xl bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-zinc-700 flex-shrink-0">
                <div class="flex items-center gap-2"><i data-lucide="notebook" class="w-4 h-4 text-amber-500"></i><h3 class="font-semibold text-slate-800 dark:text-white">Detail Jurnal</h3></div>
                <button @click="detailModal = false"><i data-lucide="x" class="w-5 h-5 text-slate-400"></i></button>
            </div>
            <div class="flex-1 overflow-y-auto p-6">
                <template x-if="detailData">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div><p class="text-xs text-slate-400 dark:text-zinc-500">Guru</p><p class="font-semibold text-slate-700 dark:text-slate-200" x-text="detailData.guru?.nama"></p></div>
                            <div><p class="text-xs text-slate-400 dark:text-zinc-500">Tanggal</p><p class="font-semibold text-slate-700 dark:text-slate-200" x-text="detailData.tanggal"></p></div>
                            <div><p class="text-xs text-slate-400 dark:text-zinc-500">Kelas</p><p class="font-semibold text-slate-700 dark:text-slate-200" x-text="detailData.kelas?.nama"></p></div>
                            <div><p class="text-xs text-slate-400 dark:text-zinc-500">Mapel</p><p class="font-semibold text-slate-700 dark:text-slate-200" x-text="detailData.mapel?.nama"></p></div>
                            <div><p class="text-xs text-slate-400 dark:text-zinc-500">Jam Masuk</p><p class="font-semibold text-slate-700 dark:text-slate-200" x-text="(detailData.jam_masuk_aktual || '-').substring(0,5)"></p></div>
                            <div><p class="text-xs text-slate-400 dark:text-zinc-500">Keterlambatan</p>
                                <p class="font-semibold" :class="detailData.is_terlambat ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'"
                                   x-text="detailData.is_terlambat ? '+' + detailData.menit_terlambat + ' menit' : 'Tepat waktu'"></p></div>
                        </div>
                        <div class="border-t border-slate-100 dark:border-zinc-700/50 pt-3">
                            <p class="text-xs text-slate-400 dark:text-zinc-500 mb-1">Materi</p>
                            <p class="text-sm text-slate-700 dark:text-slate-200 whitespace-pre-wrap" x-text="detailData.materi"></p>
                        </div>
                        <div x-show="detailData.metode_pembelajaran"><p class="text-xs text-slate-400 dark:text-zinc-500 mb-1">Metode</p><p class="text-sm text-slate-700 dark:text-slate-200" x-text="detailData.metode_pembelajaran"></p></div>
                        <div x-show="detailData.kendala"><p class="text-xs text-slate-400 dark:text-zinc-500 mb-1">Kendala</p><p class="text-sm text-slate-700 dark:text-slate-200" x-text="detailData.kendala"></p></div>
                        <div x-show="detailData.tindak_lanjut"><p class="text-xs text-slate-400 dark:text-zinc-500 mb-1">Tindak Lanjut</p><p class="text-sm text-slate-700 dark:text-slate-200" x-text="detailData.tindak_lanjut"></p></div>
                        <div x-show="detailData.lampiran && detailData.lampiran.length > 0">
                            <p class="text-xs text-slate-400 dark:text-zinc-500 mb-2">Lampiran</p>
                            <div class="flex gap-2 flex-wrap">
                                <template x-for="lmp in detailData.lampiran">
                                    <a :href="lmp.url" target="_blank" class="w-20 h-20 rounded-lg overflow-hidden bg-slate-100 dark:bg-zinc-700 hover:opacity-80 transition-opacity">
                                        <img :src="lmp.url" class="w-full h-full object-cover">
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function jurnalViewManager() {
    return {
        detailModal: false, detailData: null,
        init() { this.$nextTick(() => lucide.createIcons()); },

        async viewDetail(id) {
            const routeBase = window.location.pathname.startsWith('/ks') ? '/ks' : '/admin';
            const res = await fetch(`${routeBase}/jurnal/${id}`, { headers: { 'Accept': 'application/json' } });
            this.detailData = await res.json();
            this.detailModal = true;
            this.$nextTick(() => lucide.createIcons());
        },
    }
}
</script>
@endpush
