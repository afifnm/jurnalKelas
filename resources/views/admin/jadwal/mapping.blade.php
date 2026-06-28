@extends('layouts.app')
@section('title', 'Mapping Jadwal (Drag & Drop)')
@section('hide_sidebar', true)
@section('page-title', 'Mapping Jadwal')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Admin</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <a href="{{ route('admin.jadwal.index') }}" class="hover:text-amber-500">Jadwal</a>
    <i data-lucide="chevron-right" class="w-3 h-3"></i><span class="text-slate-700 dark:text-zinc-200 font-medium">Mapping</span>
@endsection

@section('content')
<div x-data="mappingManager()">

{{-- ===== HEADER ===== --}}
<div class="flex items-center justify-between mb-4 gap-3">
    <div class="min-w-0">
        <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white leading-tight">Mapping Jadwal</h2>
        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-0.5 truncate">Kelas {{ $kelasAktif?->nama }}</p>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        {{-- Cart Toggle Button (mobile/tablet) --}}
        <button @click="panelOpen = !panelOpen"
                class="relative lg:hidden flex items-center gap-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-medium px-3 py-2 rounded-lg transition-colors">
            <i data-lucide="layout-panel-left" class="w-4 h-4"></i>
            <span>Kartu</span>
            <span x-show="cart.length > 0"
                  x-text="cart.length"
                  class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center"></span>
        </button>
        <a href="{{ route('admin.jadwal.index', ['kelas_id' => $kelasId, 'tahun_ajaran_id' => $tahunId]) }}"
           class="btn-secondary text-xs sm:text-sm px-2.5 sm:px-3 py-2 flex items-center gap-1.5">
            <i data-lucide="layout-list" class="w-4 h-4"></i>
            <span class="hidden sm:inline">Kembali ke List</span>
            <span class="sm:hidden">List</span>
        </a>
    </div>
</div>

{{-- ===== FILTER BAR (always visible, compact) ===== --}}
<div class="card p-3 mb-4">
    <form method="GET" class="flex flex-col sm:flex-row gap-2 sm:gap-3" id="filterForm">
        <div class="flex-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Tahun Ajaran</label>
            <select name="tahun_ajaran_id" onchange="document.getElementById('filterForm').submit()"
                    class="w-full text-sm border border-slate-200 dark:border-zinc-700 rounded-lg px-2.5 py-1.5 bg-slate-50 dark:bg-zinc-900/50 text-slate-700 dark:text-zinc-200 focus:outline-none focus:border-amber-400">
                @foreach($tahunAjaran as $ta)
                    <option value="{{ $ta->id }}" @selected($tahunId == $ta->id)>
                        {{ $ta->nama }} – {{ $ta->semester }}{{ $ta->is_aktif ? ' ✓' : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Kelas</label>
            <select name="kelas_id" onchange="document.getElementById('filterForm').submit()"
                    class="w-full text-sm border border-slate-200 dark:border-zinc-700 rounded-lg px-2.5 py-1.5 bg-slate-50 dark:bg-zinc-900/50 text-slate-700 dark:text-zinc-200 focus:outline-none focus:border-amber-400">
                @foreach($kelasList as $kelas)
                    <option value="{{ $kelas->id }}" @selected($kelasId == $kelas->id)>{{ $kelas->nama }}</option>
                @endforeach
            </select>
        </div>
    </form>
</div>

{{-- ===== MAIN LAYOUT ===== --}}
<div class="flex flex-col lg:flex-row gap-4 items-start pb-32 lg:pb-8">

    {{-- ===== SIDEBAR (desktop: sticky column | mobile: slide-up panel via button) ===== --}}

    {{-- Desktop Sidebar --}}
    <div class="hidden lg:block w-72 xl:w-80 flex-shrink-0 space-y-4 sticky top-[80px] z-10">
        @include('admin.jadwal._mapping-panel')
    </div>

    {{-- Mobile/Tablet Slide-up Panel --}}
    <div x-show="panelOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="lg:hidden fixed inset-x-0 bottom-0 z-40 max-h-[75vh] flex flex-col bg-white dark:bg-zinc-900 rounded-t-2xl shadow-2xl border-t border-slate-200 dark:border-zinc-700"
         style="display: none;">
        {{-- Panel Handle & Header --}}
        <div class="flex items-center justify-between px-4 pt-3 pb-2 border-b border-slate-100 dark:border-zinc-800 flex-shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-1 bg-slate-200 dark:bg-zinc-700 rounded-full mx-auto absolute left-1/2 -translate-x-1/2 top-2"></div>
                <i data-lucide="layout-panel-left" class="w-4 h-4 text-purple-500"></i>
                <span class="text-sm font-bold text-slate-700 dark:text-white">Buat Jadwal Baru</span>
            </div>
            <button @click="panelOpen = false" class="w-7 h-7 flex items-center justify-center rounded-full bg-slate-100 dark:bg-zinc-800 text-slate-500 dark:text-zinc-400 hover:bg-slate-200">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <div class="overflow-y-auto flex-1 p-4">
            @include('admin.jadwal._mapping-panel')
        </div>
    </div>

    {{-- Mobile Panel Backdrop --}}
    <div x-show="panelOpen" @click="panelOpen = false"
         x-transition:enter="transition-opacity duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="lg:hidden fixed inset-0 z-30 bg-black/40"
         style="display: none;"></div>

    {{-- ===== GRID AREA ===== --}}
    <div class="flex-1 min-w-0 bg-white dark:bg-zinc-900 rounded-2xl shadow-sm border border-slate-200 dark:border-zinc-800 p-3 sm:p-4 relative">

        {{-- Loading Overlay --}}
        <div x-show="loading" x-transition.opacity
             class="absolute inset-0 z-10 bg-white/70 dark:bg-zinc-900/70 backdrop-blur-sm flex items-center justify-center rounded-2xl"
             style="display:none;">
            <div class="flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-zinc-800 shadow-xl rounded-full border border-slate-100 dark:border-zinc-700">
                <i data-lucide="loader-2" class="w-4 h-4 text-amber-500 animate-spin"></i>
                <span class="text-sm font-medium text-slate-600 dark:text-zinc-300">Menyimpan...</span>
            </div>
        </div>

        {{-- Hint bar --}}
        <div class="flex items-center gap-2 mb-4 p-2.5 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-100 dark:border-amber-800/30">
            <i data-lucide="info" class="w-3.5 h-3.5 text-amber-500 flex-shrink-0"></i>
            <p class="text-[11px] text-amber-700 dark:text-amber-400 leading-snug">
                <span class="hidden lg:inline">Seret kartu dari panel kiri ke slot jam, atau klik kartu lalu klik slot tujuan.</span>
                <span class="lg:hidden">Buka <b>Kartu</b>, pilih atau ketuk kartu, lalu ketuk slot jam yang diinginkan.</span>
            </p>
        </div>

        <div class="space-y-5">
            @foreach($namaHari as $hariNum => $hariNama)
                @php
                    $slots = $jamPelajaran->get($hariNum, collect());
                    if($slots->isEmpty()) continue;
                @endphp

                <div>
                    <h4 class="text-xs font-bold text-slate-700 dark:text-white mb-2.5 flex items-center gap-2 bg-slate-50 dark:bg-zinc-800/60 py-2 px-3 rounded-lg">
                        <div class="w-2 h-2 rounded-full bg-amber-400 flex-shrink-0"></div>
                        {{ $hariNama }}
                        <span class="ml-auto text-[10px] font-normal text-slate-400">{{ $slots->count() }} jam</span>
                    </h4>

                    {{-- Responsive grid: 2 col mobile → 3 col md → 4 col xl --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-2 sm:gap-3">
                        @foreach($slots as $slot)
                            @php
                                $mulai   = substr($slot->jam_mulai, 0, 5);
                                $selesai = substr($slot->jam_selesai, 0, 5);
                                $slotKey = $slot->id;
                            @endphp

                            {{-- Drop Zone --}}
                            <div class="relative rounded-xl border-2 transition-all duration-200"
                                 :class="getSlotClass('{{ $slotKey }}')"
                                 @dragover.prevent="dragOver($event)"
                                 @dragleave.prevent="dragLeave($event)"
                                 @drop.prevent="dropOnSlot($event, {{ $hariNum }}, '{{ $mulai }}', '{{ $selesai }}', '{{ $slotKey }}')"
                                 @click="handleSlotClick({{ $hariNum }}, '{{ $mulai }}', '{{ $selesai }}', '{{ $slotKey }}')">

                                {{-- Slot Header --}}
                                <div class="px-2 sm:px-3 py-1.5 flex items-center justify-between border-b border-slate-100/60 dark:border-zinc-700/40">
                                    <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 dark:text-zinc-500">Jam {{ $slot->jam_ke }}</span>
                                    <span class="text-[9px] sm:text-[10px] font-mono text-slate-500 dark:text-zinc-400 tabular-nums">{{ $mulai }}</span>
                                </div>

                                {{-- Slot Content --}}
                                <div class="p-1.5 sm:p-2 min-h-[4.5rem] flex flex-col justify-center relative group">

                                    <template x-if="slotsData['{{ $slotKey }}']">
                                        <div draggable="true"
                                             @dragstart.stop="dragStartExisting($event, slotsData['{{ $slotKey }}'], '{{ $slotKey }}')"
                                             @click.stop="selectGridItem(slotsData['{{ $slotKey }}'], '{{ $slotKey }}')"
                                             :class="selectedSourceItem?.type === 'existing' && selectedSourceItem?.source_slot_key === '{{ $slotKey }}' ? 'ring-2 ring-amber-500 ring-offset-1 dark:ring-offset-zinc-900' : ''"
                                             class="cursor-grab active:cursor-grabbing p-1.5 sm:p-2 rounded-lg bg-white dark:bg-zinc-800 shadow-sm border border-slate-200 dark:border-zinc-600 transition-colors relative select-none">

                                            {{-- Delete: always visible on touch, hover on desktop --}}
                                            <button @click.prevent.stop="deleteJadwal(slotsData['{{ $slotKey }}'].id, '{{ $slotKey }}')"
                                                    class="absolute -top-2 -right-2 w-5 h-5 sm:w-6 sm:h-6 bg-red-500 text-white rounded-full flex items-center justify-center shadow-md transition-all z-20
                                                           opacity-100 lg:opacity-0 lg:group-hover:opacity-100"
                                                    title="Hapus jadwal">
                                                <i data-lucide="x" class="w-2.5 h-2.5 sm:w-3 sm:h-3"></i>
                                            </button>

                                            <p class="text-[10px] sm:text-xs font-bold text-slate-800 dark:text-slate-100 leading-tight mb-1 line-clamp-2"
                                               x-text="slotsData['{{ $slotKey }}'].mapel_nama"></p>
                                            <p class="text-[9px] sm:text-[10px] text-slate-500 dark:text-zinc-400 flex items-start gap-0.5 sm:gap-1">
                                                <i data-lucide="user" class="w-2.5 h-2.5 sm:w-3 sm:h-3 flex-shrink-0 mt-px"></i>
                                                <span class="line-clamp-2" x-text="slotsData['{{ $slotKey }}'].guru_nama"></span>
                                            </p>
                                        </div>
                                    </template>

                                    <template x-if="!slotsData['{{ $slotKey }}']">
                                        {{-- Empty slot indicator --}}
                                        <div class="absolute inset-0 flex flex-col items-center justify-center gap-1 pointer-events-none"
                                             :class="selectedSourceItem ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'"
                                             x-transition.opacity>
                                            <div class="w-6 h-6 rounded-full border-2 border-dashed border-amber-300 dark:border-amber-700 flex items-center justify-center">
                                                <i data-lucide="plus" class="w-3 h-3 text-amber-400 dark:text-amber-600"></i>
                                            </div>
                                            <span class="text-[9px] font-medium text-amber-500 dark:text-amber-600 hidden sm:block">Taruh di sini</span>
                                        </div>
                                    </template>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ===== FLOATING BANNER: item selected, waiting for slot tap ===== --}}
<div x-show="selectedSourceItem" x-transition.opacity
     class="fixed bottom-4 inset-x-4 sm:inset-x-auto sm:left-1/2 sm:-translate-x-1/2 sm:w-auto sm:max-w-sm z-50
            bg-slate-900 dark:bg-zinc-100 text-white dark:text-slate-900
            px-4 py-3 rounded-2xl shadow-2xl border border-slate-700 dark:border-zinc-300
            flex items-center gap-3 text-sm font-medium"
     style="display: none;">
    <div class="w-8 h-8 rounded-full bg-amber-500 flex items-center justify-center flex-shrink-0">
        <i data-lucide="move" class="w-4 h-4 text-white"></i>
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-[11px] text-slate-400 dark:text-zinc-500 font-normal leading-none mb-0.5">Ketuk slot tujuan untuk menempatkan:</p>
        <p class="text-sm font-bold truncate text-white dark:text-slate-900" x-text="selectedSourceItem?.mapel_nama"></p>
    </div>
    <button @click="selectedSourceItem = null"
            class="flex-shrink-0 w-8 h-8 bg-slate-700 hover:bg-slate-600 dark:bg-zinc-300 dark:hover:bg-zinc-400 rounded-xl flex items-center justify-center transition-colors">
        <i data-lucide="x" class="w-4 h-4"></i>
    </button>
</div>

</div>
@endsection

@push('scripts')
<script>
function mappingManager() {
    return {
        source: {
            guru_id: '',
            mapel_id: ''
        },
        cart: JSON.parse(sessionStorage.getItem('mappingCart') || '[]'),
        slotsData: window.jadwalDataMap || {},
        tahunId: {{ $tahunId ?? 'null' }},
        kelasId: {{ $kelasId ?? 'null' }},
        loading: false,
        selectedSourceItem: null,
        panelOpen: false,

        init() {
            setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 100);

            this.$watch('cart', (val) => {
                sessionStorage.setItem('mappingCart', JSON.stringify(val));
                setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 10);
            });
            this.$watch('slotsData', () => {
                setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 10);
            });
        },

        getSlotClass(slotKey) {
            const hasSData   = !!this.slotsData[slotKey];
            const isTarget   = !!this.selectedSourceItem;
            const isSrcSlot  = this.selectedSourceItem?.source_slot_key === slotKey;

            if (hasSData) {
                return isSrcSlot
                    ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20'
                    : (isTarget ? 'border-slate-200 dark:border-zinc-700 bg-slate-50 dark:bg-zinc-800/50 ring-2 ring-inset ring-amber-200 dark:ring-amber-800/30 cursor-pointer'
                                : 'border-transparent bg-slate-50 dark:bg-zinc-800/50');
            } else {
                return isTarget
                    ? 'border-dashed border-amber-400 dark:border-amber-600 bg-amber-50/50 dark:bg-amber-900/10 cursor-pointer'
                    : 'border-dashed border-slate-200 dark:border-zinc-700/70 hover:border-amber-300 dark:hover:border-amber-700/50 bg-transparent';
            }
        },

        getMapelName() {
            if (!this.source.mapel_id) return '-';
            const select = document.querySelector('select[x-model="source.mapel_id"]');
            if (select && select.tomselect) {
                const opt = select.tomselect.options[this.source.mapel_id];
                return opt ? opt.text : '-';
            }
            if (!select) return '-';
            const option = select.options[select.selectedIndex];
            return option ? option.dataset.nama : '-';
        },

        getGuruName() {
            if (!this.source.guru_id) return '-';
            const select = document.querySelector('select[x-model="source.guru_id"]');
            if (select && select.tomselect) {
                const opt = select.tomselect.options[this.source.guru_id];
                return opt ? opt.text : '-';
            }
            if (!select) return '-';
            const option = select.options[select.selectedIndex];
            return option ? option.dataset.nama : '-';
        },

        addToCart() {
            if (!this.source.guru_id || !this.source.mapel_id) {
                Swal.fire({ icon: 'error', title: 'Pilih Lengkap', text: 'Pilih Mapel dan Guru terlebih dahulu.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                return;
            }
            const exists = this.cart.find(c => c.guru_id == this.source.guru_id && c.mapel_id == this.source.mapel_id);
            if (!exists) {
                this.cart.push({
                    guru_id: this.source.guru_id,
                    mapel_id: this.source.mapel_id,
                    guru_nama: this.getGuruName(),
                    mapel_nama: this.getMapelName()
                });
                // Auto-close panel on mobile after adding
                if (window.innerWidth < 1024) {
                    Swal.fire({ icon: 'success', title: 'Ditambahkan!', text: 'Sekarang ketuk kartu lalu pilih slot jam.', toast: true, position: 'top-end', showConfirmButton: false, timer: 2500 });
                }
            } else {
                Swal.fire({ icon: 'info', title: 'Sudah Ada', text: 'Kartu ini sudah ada di keranjang.', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
            }
        },

        removeFromCart(index) {
            if (this.selectedSourceItem?.type === 'new' && this.selectedSourceItem?.cartIndex === index) {
                this.selectedSourceItem = null;
            }
            this.cart.splice(index, 1);
        },

        dragStartCart(e, item) {
            e.dataTransfer.setData('application/json', JSON.stringify({
                type: 'new',
                guru_id: item.guru_id,
                mapel_id: item.mapel_id,
                guru_nama: item.guru_nama,
                mapel_nama: item.mapel_nama
            }));
            e.dataTransfer.effectAllowed = 'copy';
        },

        dragStartExisting(e, existingData, sourceSlotKey) {
            e.dataTransfer.setData('application/json', JSON.stringify({
                type: 'existing',
                jadwal_id: existingData.id,
                guru_id: existingData.guru_id,
                mapel_id: existingData.mapel_id,
                guru_nama: existingData.guru_nama,
                mapel_nama: existingData.mapel_nama,
                source_slot_key: sourceSlotKey
            }));
            e.dataTransfer.effectAllowed = 'move';
        },

        dragOver(e) {
            const dz = e.currentTarget;
            if (!dz.classList.contains('ring-2')) {
                dz.classList.add('ring-2', 'ring-amber-400', 'ring-offset-1', 'dark:ring-offset-zinc-900');
            }
        },

        dragLeave(e) {
            e.currentTarget.classList.remove('ring-2', 'ring-amber-400', 'ring-offset-1', 'dark:ring-offset-zinc-900');
        },

        async dropOnSlot(e, hari, mulai, selesai, slotKey) {
            e.currentTarget.classList.remove('ring-2', 'ring-amber-400', 'ring-offset-1', 'dark:ring-offset-zinc-900');
            const dataStr = e.dataTransfer.getData('application/json');
            if (!dataStr) return;
            const data = JSON.parse(dataStr);
            this.executePlacement(data, hari, mulai, selesai, slotKey);
        },

        handleSlotClick(hari, mulai, selesai, slotKey) {
            if (!this.selectedSourceItem) return;
            this.executePlacement(this.selectedSourceItem, hari, mulai, selesai, slotKey);
            this.selectedSourceItem = null;
            this.panelOpen = false;
        },

        executePlacement(data, hari, mulai, selesai, slotKey) {
            if (data.source_slot_key && data.source_slot_key === slotKey) return;

            const isOverwrite = !!this.slotsData[slotKey];
            const overWrittenId = isOverwrite ? this.slotsData[slotKey].id : null;

            if (data.type === 'new') {
                this.saveJadwalBaru(data.guru_id, data.mapel_id, data.guru_nama, data.mapel_nama, hari, mulai, selesai, slotKey, overWrittenId);
            } else if (data.type === 'existing') {
                this.updateJadwal(data.jadwal_id, data.guru_id, data.mapel_id, data.guru_nama, data.mapel_nama, hari, mulai, selesai, slotKey, data.source_slot_key, overWrittenId);
            }
        },

        selectCartItem(item, index) {
            if (this.selectedSourceItem?.type === 'new' && this.selectedSourceItem?.cartIndex === index) {
                this.selectedSourceItem = null;
                return;
            }
            this.selectedSourceItem = {
                type: 'new',
                guru_id: item.guru_id,
                mapel_id: item.mapel_id,
                guru_nama: item.guru_nama,
                mapel_nama: item.mapel_nama,
                cartIndex: index
            };
            // Auto-close panel on mobile so user can see the grid
            if (window.innerWidth < 1024) {
                this.panelOpen = false;
            }
        },

        selectGridItem(slotData, slotKey) {
            if (this.selectedSourceItem?.type === 'existing' && this.selectedSourceItem?.source_slot_key === slotKey) {
                this.selectedSourceItem = null;
                return;
            }
            this.selectedSourceItem = {
                type: 'existing',
                jadwal_id: slotData.id,
                guru_id: slotData.guru_id,
                mapel_id: slotData.mapel_id,
                guru_nama: slotData.guru_nama,
                mapel_nama: slotData.mapel_nama,
                source_slot_key: slotKey
            };
        },

        async saveJadwalBaru(guru_id, mapel_id, guru_nama, mapel_nama, hari, mulai, selesai, slotKey, overWrittenId) {
            if (!this.tahunId || !this.kelasId) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Tahun Ajaran atau Kelas belum dipilih.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                return;
            }
            this.loading = true;
            try {
                const res = await fetch('{{ route('admin.jadwal.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ tahun_ajaran_id: this.tahunId, kelas_id: this.kelasId, guru_id, mapel_id, jam_pelajaran_id: slotKey, overwrite_id: overWrittenId })
                });
                const responseData = await res.json();
                if (!res.ok) {
                    const msg = responseData.errors ? Object.values(responseData.errors).flat().join('\n') : (responseData.message || 'Gagal menyimpan');
                    Swal.fire({ icon: 'warning', title: 'Terjadi Bentrokan', text: msg, confirmButtonText: 'Tutup', confirmButtonColor: '#ef4444' });
                } else {
                    this.slotsData = { ...this.slotsData, [slotKey]: { id: responseData.jadwal.id, tahun_ajaran_id: this.tahunId, kelas_id: this.kelasId, guru_id, mapel_id, guru_nama, mapel_nama } };
                }
            } catch (err) {
                console.error(err);
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Koneksi ke server bermasalah.' });
            } finally {
                this.loading = false;
            }
        },

        async updateJadwal(jadwalId, guru_id, mapel_id, guru_nama, mapel_nama, hari, mulai, selesai, slotKey, sourceSlotKey, overWrittenId) {
            this.loading = true;
            try {
                const res = await fetch(`/admin/jadwal/${jadwalId}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ tahun_ajaran_id: this.tahunId, kelas_id: this.kelasId, guru_id, mapel_id, jam_pelajaran_id: slotKey, overwrite_id: overWrittenId })
                });
                const responseData = await res.json();
                if (!res.ok) {
                    const msg = responseData.errors ? Object.values(responseData.errors).flat().join('\n') : (responseData.message || 'Gagal menyimpan');
                    Swal.fire({ icon: 'warning', title: 'Terjadi Bentrokan', text: msg, confirmButtonText: 'Tutup', confirmButtonColor: '#ef4444' });
                } else {
                    const newSlotsData = { ...this.slotsData };
                    delete newSlotsData[sourceSlotKey];
                    newSlotsData[slotKey] = { id: responseData.jadwal.id, tahun_ajaran_id: this.tahunId, kelas_id: this.kelasId, guru_id, mapel_id, guru_nama, mapel_nama };
                    this.slotsData = newSlotsData;
                }
            } catch (err) {
                console.error(err);
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Koneksi ke server bermasalah.' });
            } finally {
                this.loading = false;
            }
        },

        async deleteJadwal(jadwalId, slotKey) {
            const result = await Swal.fire({
                title: 'Hapus jadwal?',
                text: 'Jadwal di jam ini akan dikosongkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            });
            if (!result.isConfirmed) return;
            this.loading = true;
            try {
                const res = await fetch(`/admin/jadwal/${jadwalId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const newSlotsData = { ...this.slotsData };
                    delete newSlotsData[slotKey];
                    this.slotsData = newSlotsData;
                    if (this.selectedSourceItem?.source_slot_key === slotKey) {
                        this.selectedSourceItem = null;
                    }
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal menghapus jadwal.' });
                }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: 'Koneksi bermasalah.' });
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

<script>
    window.jadwalDataMap = {
        @foreach($jadwalPerSlot as $slotKey => $j)
        '{{ $slotKey }}': {
            id: {{ $j->id }},
            tahun_ajaran_id: {{ $j->tahun_ajaran_id }},
            kelas_id: {{ $j->kelas_id }},
            guru_id: {{ $j->guru_id }},
            mapel_id: {{ $j->mapel_id }},
            guru_nama: @json($j->guru?->nama ?? 'Tidak Ada'),
            mapel_nama: @json($j->mapel?->nama ?? 'Tidak Ada')
        },
        @endforeach
    };
</script>
@endpush
