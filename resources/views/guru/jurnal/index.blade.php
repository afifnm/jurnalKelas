@extends('layouts.app')
@section('title', 'Jurnal Mengajar')
@section('page-title', 'Jurnal Mengajar')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Guru</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i><span class="text-slate-700 dark:text-zinc-200 font-medium">Jurnal Mengajar</span>
@endsection

@section('content')
<div x-data="jurnalManager()" x-init="init()">

    <!-- Header -->
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">Jurnal Mengajar</h2>
            <p class="text-sm text-slate-400 dark:text-zinc-500">Riwayat dan pengisian jurnal harian</p>
        </div>
        <a href="{{ route('guru.jurnal.create') }}" class="btn-primary px-5 py-2.5 text-base font-bold shadow-md">
            <i data-lucide="plus-circle" class="w-5 h-5"></i> TAMBAH JURNAL BARU
        </a>
    </div>

    <!-- Jadwal Hari Ini (shortcut) -->
    @if($jadwalHariIni->isNotEmpty())
    <div class="card p-4 mb-4">
        <p class="text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider mb-3 flex items-center gap-2">
            <i data-lucide="calendar-clock" class="w-3.5 h-3.5 text-amber-500"></i>
            Jadwal Hari Ini ({{ now()->translatedFormat('l, d M Y') }})
        </p>
        <div class="flex flex-wrap gap-2">
            @foreach($grupJadwalHariIni as $grup)
            @php
                $first      = $grup['jadwal']->first();
                $last       = $grup['jadwal']->last();
                $sudah      = count(array_intersect($grup['ids'], $sudahDiisiHariIni)) > 0;
                $jamMulai   = substr($first->jamPelajaran->jam_mulai, 0, 5);
                $jamSelesai = substr($last->jamPelajaran->jam_selesai, 0, 5);
                $jumlahJam  = $grup['jadwal']->count();
            @endphp
            @if($sudah)
            <span class="flex items-center gap-2 px-3 py-2 rounded-xl border text-sm border-green-200 dark:border-green-800/40 bg-green-50 dark:bg-green-950/30 text-green-600 dark:text-green-400 opacity-70 cursor-default">
                <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                <span class="font-medium">{{ $first->mapel->nama }}</span>
                <span class="text-xs opacity-70">{{ $first->kelas->nama }} {{ $jamMulai }}–{{ $jamSelesai }}</span>
                @if($jumlahJam > 1)<span class="text-[10px] bg-green-100 dark:bg-green-900/30 px-1.5 py-0.5 rounded-full font-medium">{{ $jumlahJam }} jam</span>@endif
                <span class="text-[10px] bg-green-100 dark:bg-green-900/30 px-1.5 py-0.5 rounded-full font-medium">Sudah diisi</span>
            </span>
            @else
            <a href="{{ route('guru.jurnal.create', ['jadwal_id' => $first->id]) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 transition-all border-amber-400 dark:border-amber-600 bg-amber-50 dark:bg-amber-950/50 text-amber-800 dark:text-amber-300 hover:bg-amber-100 dark:hover:bg-amber-900/50 shadow-sm">
                <i data-lucide="pencil" class="w-6 h-6 text-amber-600"></i>
                <div>
                    <span class="block font-bold text-base">{{ $first->mapel->nama }}</span>
                    <span class="text-sm opacity-90">Kelas {{ $first->kelas->nama }} | {{ $jamMulai }}–{{ $jamSelesai }}</span>
                </div>
                <div class="flex flex-col gap-1 items-end ml-auto">
                    @if($jumlahJam > 1)<span class="text-[10px] bg-amber-200 dark:bg-amber-800 px-2 py-0.5 rounded-md font-bold">{{ $jumlahJam }} Jam</span>@endif
                    <span class="text-[10px] bg-amber-500 text-white px-2 py-0.5 rounded-md font-bold animate-pulse">KLIK UNTUK ISI</span>
                </div>
            </a>
            @endif
            @endforeach
        </div>
    </div>
    @endif

    <!-- Filter -->
    <form method="GET" class="flex flex-wrap items-center gap-2 mb-4">
        <input type="month" name="bulan" value="{{ request('bulan') }}" class="input-field py-1.5 text-sm flex-1 min-w-[140px]">
        <select name="kelas_id" class="input-field py-1.5 text-sm flex-1 min-w-[120px]">
            <option value="">Semua Kelas</option>
            @foreach($kelas as $k)
                <option value="{{ $k->id }}" @selected(request('kelas_id') == $k->id)>{{ $k->nama }}</option>
            @endforeach
        </select>
        <div class="flex items-center gap-2">
            <button type="submit" class="btn-primary py-1.5 text-sm shrink-0">
                <i data-lucide="search" class="w-3.5 h-3.5"></i> Cari
            </button>
            @if(request()->hasAny(['bulan','kelas_id']))
            <a href="{{ route('guru.jurnal.index') }}" class="inline-flex items-center gap-1 py-1.5 px-3 text-sm rounded-lg border border-slate-200 dark:border-zinc-700 text-slate-500 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors shrink-0">
                <i data-lucide="x" class="w-3.5 h-3.5"></i> Reset
            </a>
            @endif
        </div>
    </form>

    <!-- Tabel Jurnal -->
    <div class="card overflow-hidden">

        {{-- Mobile: Card List --}}
        <div class="divide-y divide-slate-100 dark:divide-zinc-700/50 md:hidden">
            @forelse($jurnal as $j)
            @php $jamSesi = $jamSesiMap[$j->id] ?? null; @endphp
            <div class="p-4">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-700 dark:text-slate-200 text-sm">{{ $j->tanggal->translatedFormat('l, j F Y') }}</p>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">
                            <span class="font-medium">
                                <a href="{{ route('guru.jadwal.by-kelas', ['kelas_id' => $j->kelas->id]) }}" class="hover:underline hover:text-blue-600">{{ $j->kelas->nama }}</a>
                            </span> · {{ $j->mapel->nama }}
                        </p>
                    </div>
                    @if($j->isInputDalamJamMengajar($jamSesiMap[$j->id] ?? null))
                    <span class="badge badge-validated flex-shrink-0">
                        <i data-lucide="clock-check" class="w-3 h-3"></i> Dalam jam
                    </span>
                    @else
                    <span class="badge bg-red-100 dark:bg-red-950/40 text-red-700 dark:text-red-400 flex-shrink-0">
                        <i data-lucide="clock-alert" class="w-3 h-3"></i> Di luar jam
                    </span>
                    @endif
                </div>
                <p class="text-xs text-slate-600 dark:text-zinc-400 line-clamp-2 mb-3">{{ $j->materi }}</p>
                <p class="mb-2 flex items-center gap-1.5 text-xs text-slate-500 dark:text-zinc-400">
                    <i data-lucide="clock-3" class="w-3.5 h-3.5 text-amber-500"></i>
                    <span>Jam pelajaran:</span>
                    <span class="font-mono font-semibold text-slate-700 dark:text-zinc-200">
                        {{ $jamSesi ? $jamSesi['mulai'].'–'.$jamSesi['selesai'] : '-' }}
                    </span>
                </p>
                @if($jamSesi)
                    <p class="mb-2 pl-5 text-[10px] text-slate-400">
                        Jam ke-{{ $jamSesi['jam_ke_mulai'] }}–{{ $jamSesi['jam_ke_selesai'] }} · {{ $jamSesi['jumlah'] }} JP
                    </p>
                @endif
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs text-slate-400 dark:text-zinc-500 font-mono">
                        Input: {{ $j->created_at->format('H:i') }}
                        @if($j->lampiran->count())
                        · <i data-lucide="paperclip" class="w-3 h-3 inline-block -mt-0.5"></i> {{ $j->lampiran->count() }}
                        @endif
                    </span>
                    <div class="flex items-center gap-1">
                        <button @click="viewDetail({{ $j->id }})"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/30 rounded-lg transition-colors">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail
                        </button>
                        <a href="{{ route('guru.jurnal.edit', $j->id) }}"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30 rounded-lg transition-colors">
                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                        </a>
                        <button @click="deleteJurnal({{ $j->id }})"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-colors">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-4 py-12 text-center">
                <div class="flex flex-col items-center text-slate-400 dark:text-zinc-600">
                    <i data-lucide="notebook" class="w-10 h-10 mb-2 opacity-50"></i>
                    <p class="text-sm">Belum ada jurnal</p>
                    <a href="{{ route('guru.jurnal.create') }}" class="mt-3 btn-primary text-xs">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i> Isi Jurnal Pertama
                    </a>
                </div>
            </div>
            @endforelse
        </div>

        {{-- Desktop: Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-zinc-800/60 border-b border-slate-200 dark:border-zinc-700/50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider w-10">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Kelas / Mapel</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Jam Pelajaran</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Waktu Input</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Materi</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-700/50">
                    @forelse($jurnal as $j)
                    @php $jamSesi = $jamSesiMap[$j->id] ?? null; @endphp
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="px-4 py-3.5 text-slate-400 dark:text-zinc-500 text-xs">{{ $loop->iteration + ($jurnal->currentPage() - 1) * $jurnal->perPage() }}</td>
                        <td class="px-4 py-3.5">
                            <p class="font-semibold text-slate-700 dark:text-slate-200">{{ $j->tanggal->translatedFormat('l, j F Y') }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="font-medium text-slate-700 dark:text-slate-200">
                                <a href="{{ route('guru.jadwal.by-kelas', ['kelas_id' => $j->kelas->id]) }}" class="hover:underline hover:text-blue-600">{{ $j->kelas->nama }}</a>
                            </p>
                            <p class="text-xs text-slate-400">{{ $j->mapel->nama }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="font-mono font-semibold text-slate-700 dark:text-zinc-200">
                                {{ $jamSesi ? $jamSesi['mulai'].'–'.$jamSesi['selesai'] : '-' }}
                            </p>
                            @if($jamSesi)
                                <p class="mt-0.5 text-[10px] text-slate-400">
                                    Jam ke-{{ $jamSesi['jam_ke_mulai'] }}–{{ $jamSesi['jam_ke_selesai'] }} · {{ $jamSesi['jumlah'] }} JP
                                </p>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="font-mono text-sm text-slate-600 dark:text-zinc-400">{{ $j->created_at->format('H:i') }}</p>
                            <span class="text-[10px] font-medium {{ $j->isInputDalamJamMengajar($jamSesiMap[$j->id] ?? null) ? 'text-green-500 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                                {{ $j->created_at->translatedFormat('d/m/Y') }}
                            </span>
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
                                <a href="{{ route('guru.jurnal.edit', $j->id) }}"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30 rounded-lg transition-colors">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                                </a>
                                <button @click="deleteJurnal({{ $j->id }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-colors">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center">
                        <div class="flex flex-col items-center text-slate-400 dark:text-zinc-600">
                            <i data-lucide="notebook" class="w-10 h-10 mb-2 opacity-50"></i>
                            <p class="text-sm">Belum ada jurnal</p>
                            <a href="{{ route('guru.jurnal.create') }}" class="mt-3 btn-primary text-xs">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Isi Jurnal Pertama
                            </a>
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

    <!-- Modal Detail Jurnal -->
    <div x-show="detailModal" x-transition.opacity
         x-effect="document.documentElement.style.overflow = detailModal ? 'hidden' : ''; document.body.style.overflow = detailModal ? 'hidden' : ''"
         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
         @click.self="detailModal = false">
        <div x-show="detailModal" x-transition.scale.95 @click.stop
             class="w-full max-w-lg bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden max-h-[90vh]">
            <div class="overflow-y-auto max-h-[90vh]">

                <div class="sticky top-0 z-10 flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-zinc-700 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-md">
                    <div class="flex items-center gap-2">
                        <i data-lucide="eye" class="w-4 h-4 text-amber-500"></i>
                        <h3 class="font-semibold text-slate-800 dark:text-white">Detail Jurnal</h3>
                    </div>
                    <button @click="detailModal = false"><i data-lucide="x" class="w-5 h-5 text-slate-400"></i></button>
                </div>

                <div class="p-6">
                    <template x-if="detailData">
                        <div class="space-y-5">

                            <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                                <div>
                                    <p class="text-xs text-slate-400 dark:text-zinc-500 mb-0.5">Tanggal Jurnal</p>
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
                                    <p class="text-xs text-slate-400 dark:text-zinc-500 mb-0.5">Tanggal Input</p>
                                    <p class="font-semibold text-slate-700 dark:text-slate-200" x-text="detailData.tanggal_input || '-'"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 dark:text-zinc-500 mb-0.5">Jam Input</p>
                                    <p class="font-mono font-semibold text-slate-700 dark:text-slate-200" x-text="detailData.jam_input ? `${detailData.jam_input} WIB` : '-'"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 dark:text-zinc-500 mb-0.5">Jam Pelajaran</p>
                                    <p class="font-mono font-semibold text-slate-700 dark:text-slate-200" x-text="detailData.jam_sesi ? `${detailData.jam_sesi.mulai}–${detailData.jam_sesi.selesai}` : '-'"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 dark:text-zinc-500 mb-0.5">Keterlambatan</p>
                                    <p class="font-semibold"
                                       :class="detailData.dalam_jam ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
                                       x-text="detailData.status_keterlambatan || '-'"></p>
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
function jurnalManager() {
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

        async viewDetail(id) {
            this.detailData = null;
            this.detailModal = true;
            const res = await fetch(`/guru/jurnal/${id}/show`, {
                cache: 'no-store',
                headers: { 'Accept': 'application/json' }
            });
            this.detailData = await res.json();
            this.$nextTick(() => lucide.createIcons());
        },

        async deleteJurnal(id) {
            const { isConfirmed } = await Swal.fire({
                title: 'Hapus Jurnal?', icon: 'warning', showCancelButton: true,
                confirmButtonText: 'Hapus', cancelButtonText: 'Batal', confirmButtonColor: '#ef4444'
            });
            if (!isConfirmed) return;
            await fetch(`/guru/jurnal/${id}`, {
                method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
            Swal.fire({ icon: 'success', title: 'Dihapus!', timer: 1200, showConfirmButton: false, toast: true, position: 'top-end' });
            setTimeout(() => location.reload(), 1000);
        }
    }
}
</script>
@endpush
