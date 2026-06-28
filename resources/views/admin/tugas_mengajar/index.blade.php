@extends('layouts.app')
@section('title', 'Pembagian Tugas Mengajar')
@section('hide_sidebar', true)
@section('page-title', 'Pembagian Tugas Mengajar')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-1.5 hover:text-slate-800 dark:hover:text-zinc-200 transition-colors">
        <i data-lucide="home" class="w-3 h-3"></i><span>Dashboard</span>
    </a>
    <i data-lucide="chevron-right" class="w-3 h-3"></i><span class="text-slate-700 dark:text-zinc-200 font-medium">Pembagian Tugas</span>
@endsection

@push('styles')
<style>
    /* Custom Scrollbar for the matrix to make horizontal scrolling very obvious */
    .matrix-scroll::-webkit-scrollbar {
        height: 24px; /* Much thicker horizontal scrollbar for easy dragging */
        width: 14px;
    }
    .matrix-scroll::-webkit-scrollbar-track {
        background: #f1f5f9; /* slate-100 */
        border-radius: 10px;
        border: 1px solid #e2e8f0; /* slate-200 */
    }
    .dark .matrix-scroll::-webkit-scrollbar-track {
        background: #27272a; /* zinc-800 */
        border-color: #3f3f46; /* zinc-700 */
    }
    .matrix-scroll::-webkit-scrollbar-thumb {
        background: #f59e0b; /* amber-500 for high visibility */
        border-radius: 10px;
        border: 2px solid #f1f5f9; /* matches track bg to create padding effect */
    }
    .dark .matrix-scroll::-webkit-scrollbar-thumb {
        background: #fbbf24; /* amber-400 */
        border: 2px solid #27272a;
    }
    .matrix-scroll::-webkit-scrollbar-thumb:hover {
        background: #d97706; /* amber-600 */
    }
    .dark .matrix-scroll::-webkit-scrollbar-thumb:hover {
        background: #f59e0b; /* amber-500 */
    }
    
    .matrix-input {
        text-align: center;
        appearance: textfield;
        border-radius: 4px;
        border: 1px solid transparent;
        transition: all 0.2s;
    }
    .matrix-input:hover {
        border-color: #cbd5e1;
    }
    .matrix-input:focus {
        border-color: #f59e0b; /* amber-500 */
        outline: none;
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);
    }
    .matrix-input::-webkit-outer-spin-button,
    .matrix-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .matrix-input {
        -moz-appearance: textfield; /* Firefox */
    }
    .matrix-input:not(:placeholder-shown) {
        font-weight: 700;
        color: #0f172a; /* slate-900 */
    }
    .dark .matrix-input:not(:placeholder-shown) {
        color: #f8fafc; /* slate-50 */
    }
    .matrix-table th, .matrix-table td {
        border: 1px solid #e2e8f0;
    }
    .dark .matrix-table th, .dark .matrix-table td {
        border-color: #3f3f46;
    }
    .sticky-col {
        position: sticky;
        z-index: 10;
    }
    .sticky-thead {
        position: sticky;
        top: 0;
        z-index: 20;
        background-color: #ecfdf5; /* emerald-50 */
    }
    .dark .sticky-thead {
        background-color: #064e3b; /* emerald-900 */
    }
    .sticky-col-header {
        z-index: 30; /* Overlaps header and col */
        background-color: #ecfdf5;
    }
    .dark .sticky-col-header {
        background-color: #064e3b;
    }
    
    /* Z-index layers:
       - Header row: 20
       - Fixed cols (No, Guru, Mapel): 10
       - Intersection (Fixed headers): 30
    */
</style>
@endpush

@section('content')
<div x-data="tugasMengajarManager()" x-init="initData()">

<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-3">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.dashboard') }}" class="flex-shrink-0 p-2 rounded-lg bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 hover:bg-slate-50 dark:hover:bg-zinc-700 text-slate-500 dark:text-zinc-400 transition-colors shadow-sm" title="Kembali ke Dashboard">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white leading-tight">Pembagian Tugas Mengajar Guru</h2>
            <p class="text-xs text-slate-400 dark:text-zinc-500 mt-0.5">
                Tahun Ajaran Aktif: <span class="font-medium text-amber-500">{{ $tahunAjaranAktif->nama }} - {{ $tahunAjaranAktif->semester }}</span>
            </p>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <!-- Print Button -->
        <a href="{{ route('admin.tugas-mengajar.print') }}" target="_blank" class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-800/50 hover:bg-indigo-100 dark:hover:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 transition-colors shadow-sm" title="Cetak HTML">
            <i data-lucide="printer" class="w-5 h-5"></i>
        </a>
        
        <!-- Auto Save Toggle -->
        <label class="flex items-center gap-2 cursor-pointer bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 px-3 py-1.5 rounded-lg shadow-sm">
            <span class="text-xs font-medium transition-colors" :class="autoSave ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-zinc-400'" x-text="autoSave ? 'Auto Save: ON' : 'Auto Save: OFF'"></span>
            <div class="relative flex items-center">
                <input type="checkbox" x-model="autoSave" class="sr-only">
                <div class="block w-8 h-4.5 rounded-full transition-colors" :class="autoSave ? 'bg-emerald-500' : 'bg-slate-200 dark:bg-zinc-700'"></div>
                <div class="dot absolute left-1 bg-white w-3 h-3 rounded-full transition-transform" :class="{'translate-x-3.5': autoSave}"></div>
            </div>
        </label>

        <!-- Simpan Button (only if Auto Save OFF) -->
        <template x-if="!autoSave">
            <button @click="saveAll()" class="btn-primary text-xs sm:text-sm px-3 py-1.5 flex items-center gap-1.5 shadow-sm" :disabled="loading || dirtyRows.length === 0">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span x-text="loading ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                <span x-show="dirtyRows.length > 0" class="ml-1 bg-white/20 px-1.5 py-0.5 rounded-full text-[10px]" x-text="dirtyRows.length"></span>
            </button>
        </template>
    </div>
</div>

{{-- MATRIX WRAPPER --}}
<div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-slate-200 dark:border-zinc-800 overflow-hidden relative">
    
    {{-- Loading overlay --}}
    <div x-show="loading" x-transition.opacity
         class="absolute inset-0 z-50 bg-white/50 dark:bg-zinc-900/50 backdrop-blur-sm flex items-center justify-center pointer-events-none"
         style="display:none;">
         <div class="bg-white dark:bg-zinc-800 px-4 py-2 rounded-full shadow-lg border border-slate-100 dark:border-zinc-700 flex items-center gap-2">
            <i data-lucide="loader-2" class="w-4 h-4 text-amber-500 animate-spin"></i>
            <span class="text-xs font-medium">Menyimpan...</span>
         </div>
    </div>

    <div class="overflow-auto matrix-scroll max-h-[calc(100vh-170px)] relative">
        <table class="matrix-table w-full text-left border-collapse min-w-max text-xs">
            <thead class="sticky-thead">
                {{-- ROW 1: Group Headers --}}
                <tr class="bg-emerald-50 dark:bg-emerald-900/20 text-emerald-800 dark:text-emerald-400">
                    <th rowspan="3" class="sticky-col sticky-col-header left-0 w-[40px] min-w-[40px] max-w-[40px] px-2 py-1.5 text-center font-bold uppercase tracking-wider">NO</th>
                    <th rowspan="3" class="sticky-col sticky-col-header left-[40px] w-[80px] min-w-[80px] max-w-[80px] px-2 py-1.5 text-center font-bold uppercase tracking-wider cursor-pointer hover:bg-emerald-100 dark:hover:bg-emerald-800/30 transition-colors" @click="sortBy('guru_kode')">
                        KODE<br>GURU
                        <i data-lucide="arrow-up-down" class="w-3 h-3 inline-block ml-1 opacity-50"></i>
                    </th>
                    <th rowspan="3" class="sticky-col sticky-col-header left-[120px] w-[180px] min-w-[180px] max-w-[180px] px-3 py-1.5 font-bold uppercase tracking-wider cursor-pointer hover:bg-emerald-100 dark:hover:bg-emerald-800/30 transition-colors" @click="sortBy('guru_nama')">
                        NAMA GURU
                        <i data-lucide="arrow-up-down" class="w-3 h-3 inline-block ml-1 opacity-50"></i>
                    </th>
                    <th rowspan="3" class="sticky-col sticky-col-header left-[300px] w-[180px] min-w-[180px] max-w-[180px] px-3 py-1.5 font-bold uppercase tracking-wider">MATA PELAJARAN</th>
                    <th rowspan="3" class="sticky-col sticky-col-header left-[480px] w-[60px] min-w-[60px] max-w-[60px] px-2 py-1.5 text-center font-bold uppercase tracking-wider border-r border-emerald-200 dark:border-emerald-800/50">KODE</th>
                    
                    <th colspan="{{ count($kelasList) }}" class="px-3 py-1 text-center font-bold uppercase tracking-wider border-b border-emerald-200 dark:border-emerald-800/50">FASE / TINGKAT / KELAS</th>
                    
                    <th rowspan="3" class="px-1 py-1.5 w-[50px] min-w-[50px] max-w-[50px] text-center font-bold uppercase tracking-wider bg-purple-50 dark:bg-purple-900/20 text-purple-800 dark:text-purple-400 text-[10px] leading-tight">JAB.<br>KHUS</th>
                    <th rowspan="3" class="px-2 py-1.5 w-[50px] min-w-[50px] max-w-[50px] text-center font-bold uppercase tracking-wider bg-amber-50 dark:bg-amber-900/20 text-amber-800 dark:text-amber-400 shadow-[-4px_0_10px_rgba(0,0,0,0.05)] text-[10px] leading-tight">JML<br>JP</th>
                    <th rowspan="3" class="px-2 py-1.5 w-[50px] min-w-[50px] max-w-[50px] text-center font-bold uppercase tracking-wider bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400">AKSI</th>
                </tr>
                
                {{-- ROW 2: Tingkat --}}
                <tr class="bg-emerald-50 dark:bg-emerald-900/20 text-emerald-800 dark:text-emerald-400">
                    @foreach($kelasGrouped as $tingkat => $kelasArray)
                        <th colspan="{{ count($kelasArray) }}" class="px-2 py-1 text-center font-bold text-xs border-b border-emerald-200 dark:border-emerald-800/50">
                            @if($tingkat == 'X') E / X
                            @elseif($tingkat == 'XI') F / XI
                            @else {{ $tingkat }} @endif
                        </th>
                    @endforeach
                </tr>

                {{-- ROW 3: Class Names --}}
                <tr class="bg-emerald-50/50 dark:bg-emerald-900/10 text-emerald-800 dark:text-emerald-400">
                    @foreach($kelasGrouped as $tingkat => $kelasArray)
                        @foreach($kelasArray as $kelas)
                            <th class="px-2 py-1 text-center font-bold text-[10px]" title="{{ $kelas->nama }}">
                                {{ str_replace("$tingkat ", "", $kelas->nama) }}
                            </th>
                        @endforeach
                    @endforeach
                </tr>
            </thead>
            
            <tbody class="text-slate-700 dark:text-zinc-300">
                <template x-for="(row, index) in rows" :key="row.id_key">
                    <tr class="hover:bg-slate-50 dark:hover:bg-zinc-800/50 transition-colors group">
                        
                        <template x-if="isFirstRowOfGuru(row.guru_id, index)">
                            <td :rowspan="getRowCountOfGuru(row.guru_id)" class="sticky-col bg-white dark:bg-zinc-900 left-0 w-[40px] min-w-[40px] max-w-[40px] px-2 py-2 text-center text-[11px] border-b border-slate-200 dark:border-zinc-700 align-top" x-text="getGuruIndex(row.guru_id)"></td>
                        </template>

                        <template x-if="isFirstRowOfGuru(row.guru_id, index)">
                            <td :rowspan="getRowCountOfGuru(row.guru_id)" class="sticky-col bg-white dark:bg-zinc-900 left-[40px] w-[80px] min-w-[80px] max-w-[80px] px-2 py-2 text-center font-medium font-mono text-[11px] text-emerald-600 dark:text-emerald-400 border-b border-slate-200 dark:border-zinc-700 align-top">
                                <span x-text="row.guru_kode" :title="row.guru_kode"></span>
                            </td>
                        </template>

                        <template x-if="isFirstRowOfGuru(row.guru_id, index)">
                            <td :rowspan="getRowCountOfGuru(row.guru_id)" class="sticky-col bg-white dark:bg-zinc-900 left-[120px] w-[180px] min-w-[180px] max-w-[180px] px-3 py-2 font-medium whitespace-nowrap overflow-hidden text-ellipsis border-b border-r border-slate-200 dark:border-zinc-700 align-top">
                                <span x-text="row.guru_nama" :title="row.guru_nama"></span>
                            </td>
                        </template>

                        <td class="sticky-col bg-white dark:bg-zinc-900 left-[300px] w-[180px] min-w-[180px] max-w-[180px] px-3 py-1 text-[11px] whitespace-nowrap overflow-hidden text-ellipsis border-b border-slate-200 dark:border-zinc-700">
                            <span class="text-slate-400 italic" x-show="!row.mapel_id">Belum ada mapel</span>
                            <span x-show="row.mapel_id" x-text="row.mapel_nama" :title="row.mapel_nama"></span>
                        </td>
                        <td class="sticky-col bg-white dark:bg-zinc-900 left-[480px] w-[60px] min-w-[60px] max-w-[60px] px-2 py-1 text-center text-[10px] font-mono text-slate-500 border-r border-b border-slate-200 dark:border-zinc-700">
                            <span x-show="!row.mapel_id">-</span>
                            <span x-show="row.mapel_id" x-text="row.mapel_kode"></span>
                        </td>
                        
                        {{-- Input Jam per Kelas --}}
                        @php $colIndex = 0; @endphp
                        @foreach($kelasGrouped as $tingkat => $kelasArray)
                            @foreach($kelasArray as $kelas)
                                <td class="p-0 text-center relative group/cell border-b border-slate-200 dark:border-zinc-700">
                                    <input type="number" min="0" max="40" placeholder="-"
                                           class="matrix-input w-full h-full min-h-[32px] bg-transparent text-sm font-medium dark:text-white placeholder:text-slate-300 dark:placeholder:text-zinc-600 focus:bg-amber-50 dark:focus:bg-amber-900/20 disabled:opacity-30 disabled:bg-slate-50 dark:disabled:bg-zinc-800 disabled:cursor-not-allowed"
                                           x-model.number="row.kelas_hours[{{ $kelas->id }}]"
                                           @change="saveCell(row, {{ $kelas->id }})"
                                           @focus="$event.target.select()"
                                           @keydown="handleKeydown($event, index, {{ $colIndex }})"
                                           :disabled="!row.mapel_id"
                                           :data-r="index" data-c="{{ $colIndex }}">
                                </td>
                                @php $colIndex++; @endphp
                            @endforeach
                        @endforeach

                        {{-- Jabatan Khusus --}}
                        <template x-if="isFirstRowOfGuru(row.guru_id, index)">
                            <td :rowspan="getRowCountOfGuru(row.guru_id)" class="px-1 py-1 w-[50px] max-w-[50px] text-center bg-purple-50/30 dark:bg-purple-900/10 border-l border-b border-slate-200 dark:border-zinc-700 align-top">
                                <div class="flex flex-col gap-1 items-center justify-start mt-1">
                                    <input type="number" min="0" class="w-9 text-[11px] text-center font-bold text-purple-600 dark:text-purple-400 border-slate-200 dark:border-zinc-700 rounded px-0 py-1 bg-white dark:bg-zinc-800" 
                                           placeholder="-" 
                                           x-model.number="getJabatanData(row.guru_id).jumlah_jam"
                                           @change="saveJabatan(row.guru_id)">
                                </div>
                            </td>
                        </template>

                        {{-- Total JP (Row -> Guru) --}}
                        <template x-if="isFirstRowOfGuru(row.guru_id, index)">
                            <td :rowspan="getRowCountOfGuru(row.guru_id)" class="px-2 py-2 text-center bg-amber-50/50 dark:bg-amber-900/10 shadow-[-4px_0_10px_rgba(0,0,0,0.02)] border-b border-slate-200 dark:border-zinc-700 align-top">
                                <span class="font-bold text-[14px] text-amber-600 dark:text-amber-400" x-text="calculateTotalGuru(row.guru_id)"></span>
                            </td>
                        </template>
                        
                        {{-- Aksi Delete / Tambah --}}
                        <td class="px-2 py-1 text-center bg-white dark:bg-zinc-900 border-b border-slate-200 dark:border-zinc-700 align-middle">
                            <div class="flex items-center justify-center gap-1.5">
                                <template x-if="isFirstRowOfGuru(row.guru_id, index)">
                                    <button @click="openAddMapelModal(row.guru_id)" class="p-1.5 rounded-md text-emerald-600 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400 dark:hover:bg-emerald-900/50 transition-colors" title="Tambah Mapel untuk Guru ini">
                                        <i data-lucide="plus" class="w-4 h-4"></i>
                                    </button>
                                </template>
                                
                                <template x-if="row.mapel_id">
                                    <button @click="deleteRow(row.guru_id, row.mapel_id, index)" class="p-1.5 rounded-md text-red-500 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 transition-colors" title="Hapus Mapel ini">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </template>
                            </div>
                        </td>
                    </tr>
                </template>
                <tr x-show="rows.length === 0">
                    <td colspan="100%" class="text-center py-8 text-slate-500">Belum ada data pembagian tugas. Klik "Tambah Penugasan" untuk memulai.</td>
                </tr>
            </tbody>
            
            {{-- FOOTER: Totals per Column --}}
            <tfoot class="font-bold bg-slate-50 dark:bg-zinc-800 text-slate-700 dark:text-white sticky bottom-0 z-20 shadow-[0_-4px_10px_rgba(0,0,0,0.05)]">
                <tr>
                    <td colspan="5" class="sticky-col bg-slate-50 dark:bg-zinc-800 left-0 px-3 py-2 text-right">TOTAL JP KELAS</td>
                    
                    @foreach($kelasGrouped as $tingkat => $kelasArray)
                        @foreach($kelasArray as $kelas)
                            <td class="px-2 py-2 text-center text-[13px] text-emerald-600 dark:text-emerald-400" x-text="calculateColumnTotal({{ $kelas->id }})"></td>
                        @endforeach
                    @endforeach

                    <td class="px-3 py-2 text-right text-purple-600 dark:text-purple-400" x-text="calculateAllJabatan()"></td>
                    <td class="px-2 py-2 text-center text-amber-600 dark:text-amber-400 bg-amber-100 dark:bg-amber-900/30" x-text="calculateGrandTotal()"></td>
                    <td class="bg-slate-50 dark:bg-zinc-800"></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- MODAL TAMBAH BARIS --}}
<div x-show="openModalBaris" style="display:none;"
     class="fixed inset-0 z-[100] flex items-center justify-center">
    <div x-show="openModalBaris" @click="openModalBaris = false" x-transition.opacity class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div x-show="openModalBaris" x-transition class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-zinc-800 w-full max-w-md relative z-10 p-5 overflow-visible">
        
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg text-slate-800 dark:text-white">Tambah Mata Pelajaran</h3>
            <button @click="openModalBaris = false" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <div class="space-y-4">
            <div class="p-3 bg-slate-50 dark:bg-zinc-800/50 rounded-lg border border-slate-100 dark:border-zinc-800">
                <div class="text-xs text-slate-500 mb-1">Guru yang dipilih:</div>
                <div class="font-bold text-slate-800 dark:text-white" x-text="getGuruNameForModal()"></div>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-zinc-400 mb-1">Mata Pelajaran</label>
                <select x-model="newRow.mapel_id" class="tomselect-init w-full text-sm border-slate-200 dark:border-zinc-700 rounded-lg bg-slate-50 dark:bg-zinc-900/50">
                    <option value="">Pilih Mapel...</option>
                    @foreach($mapelList as $mapel)
                        <option value="{{ $mapel->id }}" data-nama="{{ $mapel->nama }}" data-kode="{{ $mapel->kode }}">{{ $mapel->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-2">
            <button @click="openModalBaris = false" class="btn-secondary px-4 py-2 text-sm">Batal</button>
            <button @click="addRow()" class="btn-primary px-4 py-2 text-sm">Tambahkan</button>
        </div>
    </div>
</div>

</div>
@endsection

@push('scripts')
<script>
function tugasMengajarManager() {
    return {
        rows: [],
        jabatan: {},
        kelasIds: @json($kelasList->pluck('id')),
        loading: false,
        
        autoSave: true,
        dirtyRows: [],
        
        sortColumn: 'guru_kode',
        sortDirection: 'asc',

        openModalBaris: false,
        newRow: {
            guru_id: '',
            mapel_id: ''
        },

        initData() {
            this.rows = @json($rowsData);
            this.jabatan = @json($jabatanData);
            
            // Ensure classes objects exist so v-model doesn't break
            this.rows.forEach(row => {
                if(typeof row.kelas_hours !== 'object' || Array.isArray(row.kelas_hours)) {
                    row.kelas_hours = {};
                }
            });

            // Initialize select after modal opens
            this.$watch('openModalBaris', (val) => {
                if (val) {
                    setTimeout(() => {
                        const selects = document.querySelectorAll('.tomselect-init:not(.tomselected)');
                        selects.forEach(el => {
                            new TomSelect(el, { create: false, sortField: {field: "text", direction: "asc"} });
                        });
                    }, 50);
                }
            });
            
            this.sortRows();
            
            setTimeout(() => { lucide.createIcons(); }, 100);
        },

        sortBy(col) {
            if (this.sortColumn === col) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortColumn = col;
                this.sortDirection = 'asc';
            }
            this.sortRows();
        },

        sortRows() {
            this.rows.sort((a, b) => {
                let valA = a[this.sortColumn];
                let valB = b[this.sortColumn];
                
                if (typeof valA === 'string') valA = valA.toLowerCase();
                if (typeof valB === 'string') valB = valB.toLowerCase();
                
                if (valA < valB) return this.sortDirection === 'asc' ? -1 : 1;
                if (valA > valB) return this.sortDirection === 'asc' ? 1 : -1;
                return 0;
            });
        },

        isFirstRowOfGuru(guruId, index) {
            // Find the first index in rows where guru_id matches
            const firstIndex = this.rows.findIndex(r => r.guru_id == guruId);
            return firstIndex === index;
        },

        getRowCountOfGuru(guruId) {
            return this.rows.filter(r => r.guru_id == guruId).length;
        },

        getGuruIndex(guruId) {
            // Because rows might be filtered or just many duplicates, we find the unique teachers
            const uniqueGurus = [...new Set(this.rows.map(r => r.guru_id))];
            return uniqueGurus.indexOf(guruId) + 1;
        },

        getJabatanData(guruId) {
            if (!this.jabatan[guruId]) {
                this.jabatan[guruId] = { jumlah_jam: '' };
            }
            return this.jabatan[guruId];
        },

        handleKeydown(e, r, c) {
            let nextR = r;
            let nextC = c;
            
            if (e.key === 'ArrowUp') {
                nextR = r - 1;
            } else if (e.key === 'ArrowDown') {
                nextR = r + 1;
            } else if (e.key === 'ArrowLeft') {
                nextC = c - 1;
            } else if (e.key === 'ArrowRight') {
                nextC = c + 1;
            } else {
                return;
            }
            
            const target = document.querySelector(`input[data-r="${nextR}"][data-c="${nextC}"]`);
            if (target) {
                e.preventDefault();
                target.focus();
                target.select();
            }
        },

        calculateTotalGuru(guruId) {
            let total = 0;
            // Sum all mapels for this guru
            this.rows.filter(r => r.guru_id == guruId).forEach(row => {
                this.kelasIds.forEach(id => {
                    const val = parseInt(row.kelas_hours[id]);
                    if (!isNaN(val)) total += val;
                });
            });
            // Add Jabatan Khusus
            const jk = this.getJabatanData(guruId);
            const jkJam = parseInt(jk.jumlah_jam);
            if (!isNaN(jkJam)) total += jkJam;
            
            return total;
        },

        calculateColumnTotal(kelasId) {
            let total = 0;
            this.rows.forEach(row => {
                const val = parseInt(row.kelas_hours[kelasId]);
                if (!isNaN(val)) total += val;
            });
            return total;
        },

        calculateAllJabatan() {
            let total = 0;
            Object.values(this.jabatan).forEach(jk => {
                const val = parseInt(jk.jumlah_jam);
                if (!isNaN(val)) total += val;
            });
            return total;
        },

        calculateGrandTotal() {
            let total = 0;
            this.kelasIds.forEach(id => {
                total += this.calculateColumnTotal(id);
            });
            total += this.calculateAllJabatan();
            return total;
        },

        async saveCell(row, kelasId) {
            let val = parseInt(row.kelas_hours[kelasId]);
            if (isNaN(val) || val < 0) {
                val = 0;
                row.kelas_hours[kelasId] = '';
            }
            
            if (!this.autoSave) {
                const idx = this.dirtyRows.findIndex(d => d.guru_id === row.guru_id && d.mapel_id === row.mapel_id && d.kelas_id === kelasId);
                if (idx > -1) {
                    this.dirtyRows[idx].jumlah_jam = val;
                } else {
                    this.dirtyRows.push({ guru_id: row.guru_id, mapel_id: row.mapel_id, kelas_id: kelasId, jumlah_jam: val });
                }
                return;
            }
            
            this.loading = true;
            try {
                const res = await fetch('{{ route('admin.tugas-mengajar.update-cell') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        guru_id: row.guru_id,
                        mapel_id: row.mapel_id,
                        kelas_id: kelasId,
                        jumlah_jam: val
                    })
                });
                
                if (!res.ok) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal menyimpan data.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                }
            } catch (err) {
                console.error(err);
            } finally {
                this.loading = false;
            }
        },

        async saveAll() {
            if (this.dirtyRows.length === 0) return;
            
            this.loading = true;
            try {
                const res = await fetch('{{ route('admin.tugas-mengajar.bulk-update') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ changes: this.dirtyRows })
                });
                
                if (res.ok) {
                    this.dirtyRows = [];
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Perubahan berhasil disimpan.', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal menyimpan data.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                }
            } catch (err) {
                console.error(err);
            } finally {
                this.loading = false;
            }
        },

        async saveJabatan(guruId) {
            const jk = this.getJabatanData(guruId);
            
            this.loading = true;
            try {
                const res = await fetch('{{ route('admin.tugas-mengajar.update-jabatan') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        guru_id: guruId,
                        jumlah_jam: jk.jumlah_jam
                    })
                });
                
                if (!res.ok) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal menyimpan jabatan.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                }
            } catch (err) {
                console.error(err);
            } finally {
                this.loading = false;
            }
        },

        getGuruNameForModal() {
            if (!this.newRow.guru_id) return '';
            const row = this.rows.find(r => r.guru_id === this.newRow.guru_id);
            return row ? row.guru_nama : '';
        },

        openAddMapelModal(guruId) {
            this.newRow.guru_id = guruId;
            this.newRow.mapel_id = '';
            
            this.openModalBaris = true;
            
            // Wait for modal to render then initialize tomselect on mapel_id if not already
            setTimeout(() => {
                const mapelSel = document.querySelector('select[x-model="newRow.mapel_id"]');
                if (mapelSel && !mapelSel.tomselect) {
                    new TomSelect(mapelSel, { create: false, sortField: {field: "text", direction: "asc"} });
                }
            }, 50);
        },

        addRow() {
            if (!this.newRow.guru_id || !this.newRow.mapel_id) {
                Swal.fire({ icon: 'warning', text: 'Pilih Mapel', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                return;
            }

            const key = this.newRow.guru_id + '-' + this.newRow.mapel_id;
            const exists = this.rows.find(r => r.id_key === key);
            
            if (exists) {
                Swal.fire({ icon: 'info', text: 'Mapel ini sudah ada di daftar penugasan guru.', toast: true, position: 'top-end', showConfirmButton: false, timer: 2500 });
                this.openModalBaris = false;
                return;
            }

            let guruName = '-', guruKode = '-', mapelName = '-', mapelKode = '-';
            
            // Get Guru details from existing rows
            const existingRow = this.rows.find(r => r.guru_id === this.newRow.guru_id);
            if (existingRow) {
                guruName = existingRow.guru_nama;
                guruKode = existingRow.guru_kode;
            }
            
            const mapelSel = document.querySelector('select[x-model="newRow.mapel_id"]');
            if (mapelSel && mapelSel.tomselect) {
                const opt = mapelSel.options[mapelSel.selectedIndex];
                mapelName = mapelSel.tomselect.options[this.newRow.mapel_id]?.text || '-';
                const originalOpt = Array.from(mapelSel.options).find(o => o.value == this.newRow.mapel_id);
                if (originalOpt) mapelKode = originalOpt.dataset.kode || '-';
            }

            // Replace the dummy row if it exists
            const dummyIndex = this.rows.findIndex(r => r.guru_id == this.newRow.guru_id && !r.mapel_id);
            if (dummyIndex > -1) {
                this.rows[dummyIndex].id_key = key;
                this.rows[dummyIndex].mapel_id = this.newRow.mapel_id;
                this.rows[dummyIndex].mapel_nama = mapelName;
                this.rows[dummyIndex].mapel_kode = mapelKode;
                this.rows[dummyIndex].kelas_hours = {};
            } else {
                this.rows.push({
                    id_key: key,
                    guru_id: this.newRow.guru_id,
                    mapel_id: this.newRow.mapel_id,
                    guru_nama: guruName,
                    guru_kode: guruKode,
                    mapel_nama: mapelName,
                    mapel_kode: mapelKode,
                    kelas_hours: {}
                });
            }
            
            this.sortRows();

            this.openModalBaris = false;
            this.newRow.guru_id = '';
            this.newRow.mapel_id = '';
            
            // Re-init tomselect if needed, but we don't need to clear them if we recreate modal.
            setTimeout(() => { lucide.createIcons(); }, 50);
        },
        
        async deleteRow(guruId, mapelId, index) {
            const result = await Swal.fire({
                title: 'Hapus Penugasan?',
                text: 'Data jam pelajaran untuk kombinasi ini akan dihapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            });
            
            if (!result.isConfirmed) return;
            
            this.loading = true;
            try {
                const res = await fetch('{{ route('admin.tugas-mengajar.destroy-row') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ guru_id: guruId, mapel_id: mapelId })
                });
                
                if (res.ok) {
                    this.rows.splice(index, 1);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal menghapus penugasan.' });
                }
            } catch (err) {
                console.error(err);
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endpush
