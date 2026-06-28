@extends('layouts.app')
@section('title', 'Mata Pelajaran')
@section('page-title', 'Mata Pelajaran')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Admin</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i><span class="text-slate-700 dark:text-zinc-200 font-medium">Mata Pelajaran</span>
@endsection

@section('content')
<div x-data="mapelManager()" x-init="init()">
    <div class="flex items-start justify-between gap-3 mb-5">
        <div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">Mata Pelajaran</h2>
            <p class="text-sm text-slate-400 dark:text-zinc-500">Kelola daftar mata pelajaran</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <button @click="openImport()" class="btn-secondary">
                <i data-lucide="upload" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Import</span>
            </button>
            <button @click="openCreate()" class="btn-primary">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Tambah Mapel</span>
            </button>
        </div>
    </div>

    <div class="card overflow-hidden">

        {{-- Mobile: Card List --}}
        <div class="divide-y divide-slate-100 dark:divide-zinc-700/50 md:hidden">
            @forelse($mapel as $item)
            <div class="flex items-center gap-3 p-4">
                <div class="w-9 h-9 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="book-marked" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-700 dark:text-slate-200 text-sm truncate">{{ $item->nama }}</p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        @if($item->kode)
                        <code class="text-[10px] bg-slate-100 dark:bg-zinc-700 px-1.5 py-0.5 rounded text-slate-600 dark:text-zinc-300">{{ $item->kode }}</code>
                        @endif
                        <span class="badge bg-slate-100 dark:bg-zinc-700 text-slate-600 dark:text-zinc-300">{{ $item->jadwal_count }} jadwal</span>
                    </div>
                </div>
                <div class="flex items-center gap-1 flex-shrink-0">
                    <button @click="openPengajar({{ $item->id }}, '{{ addslashes($item->nama) }}')"
                        class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/30 rounded-lg transition-colors">
                        <i data-lucide="users" class="w-3.5 h-3.5"></i>
                    </button>
                    <button @click="openEdit({{ $item->toJson() }})"
                        class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30 rounded-lg transition-colors">
                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                    </button>
                    <button @click="deleteMapel({{ $item->id }}, '{{ addslashes($item->nama) }}')"
                        class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-colors">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            </div>
            @empty
            <div class="px-4 py-12 text-center text-slate-400 dark:text-zinc-600">
                <div class="flex flex-col items-center"><i data-lucide="inbox" class="w-10 h-10 mb-2 opacity-50"></i><p class="text-sm">Belum ada mata pelajaran</p></div>
            </div>
            @endforelse
        </div>

        {{-- Desktop: Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-zinc-800/60 border-b border-slate-200 dark:border-zinc-700/50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider w-12">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Nama Mapel</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Kode</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Jadwal</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-700/50">
                    @forelse($mapel as $item)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="px-4 py-3.5 text-slate-400 dark:text-zinc-500 text-xs">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center">
                                    <i data-lucide="book-marked" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                                </div>
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $item->nama }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <code class="text-xs bg-slate-100 dark:bg-zinc-700 px-2 py-0.5 rounded text-slate-600 dark:text-zinc-300">{{ $item->kode ?? '-' }}</code>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="badge bg-slate-100 dark:bg-zinc-700 text-slate-600 dark:text-zinc-300">{{ $item->jadwal_count }} jadwal</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                <button @click="openPengajar({{ $item->id }}, '{{ addslashes($item->nama) }}')" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/30 rounded-lg transition-colors">
                                    <i data-lucide="users" class="w-3.5 h-3.5"></i> Pengajar
                                </button>
                                <button @click="openEdit({{ $item->toJson() }})" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30 rounded-lg transition-colors">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                                </button>
                                <button @click="deleteMapel({{ $item->id }}, '{{ addslashes($item->nama) }}')" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-colors">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-slate-400 dark:text-zinc-600">
                        <div class="flex flex-col items-center"><i data-lucide="inbox" class="w-10 h-10 mb-2 opacity-50"></i><p class="text-sm">Belum ada mata pelajaran</p></div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($mapel->hasPages())
        <div class="px-4 py-3 border-t border-slate-100 dark:border-zinc-700/50">{{ $mapel->links() }}</div>
        @endif
    </div>

    <!-- Modal -->
    <div x-show="modal" x-transition.opacity
         x-effect="document.documentElement.style.overflow = modal ? 'hidden' : ''; document.body.style.overflow = modal ? 'hidden' : ''"
         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
         @click.self="modal = false">
        <div x-show="modal" x-transition.scale.95 @click.stop class="w-full max-w-sm bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-zinc-700">
                <div class="flex items-center gap-2"><i data-lucide="book-marked" class="w-4 h-4 text-amber-500"></i>
                    <h3 class="font-semibold text-slate-800 dark:text-white" x-text="mode === 'create' ? 'Tambah Mapel' : 'Edit Mapel'"></h3></div>
                <button @click="modal = false"><i data-lucide="x" class="w-5 h-5 text-slate-400"></i></button>
            </div>
            <form @submit.prevent="submitForm()" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="book-marked" class="w-3.5 h-3.5 inline mr-1"></i>Nama Mapel</label>
                    <input type="text" x-model="form.nama" class="input-field" placeholder="Nama mata pelajaran" required>
                    <p x-show="errors.nama" x-text="errors.nama" class="text-xs text-red-500 mt-1"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="tag" class="w-3.5 h-3.5 inline mr-1"></i>Kode <span class="text-slate-400">(opsional)</span></label>
                    <input type="text" x-model="form.kode" class="input-field" placeholder="MTK">
                    <p x-show="errors.kode" x-text="errors.kode" class="text-xs text-red-500 mt-1"></p>
                </div>
                <p x-show="errorMsg" x-text="errorMsg" class="text-xs text-red-500"></p>
                <div class="flex gap-3 pt-1">
                    <button type="button" @click="modal = false" class="btn-secondary flex-1">Batal</button>
                    <button type="submit" :disabled="loading" class="btn-primary flex-1">
                        <i data-lucide="save" class="w-4 h-4" x-show="!loading"></i>
                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin" x-show="loading"></i>
                        <span x-text="loading ? 'Menyimpan...' : 'Simpan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Pengajar Modal -->
    <div x-show="pengajarModal" x-transition.opacity
         x-effect="document.documentElement.style.overflow = pengajarModal ? 'hidden' : ''; document.body.style.overflow = pengajarModal ? 'hidden' : ''"
         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
         @click.self="pengajarModal = false">
        <div x-show="pengajarModal" x-transition.scale.95 @click.stop
             class="w-full max-w-lg bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[80vh]">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-zinc-700 flex-shrink-0">
                <div class="flex items-center gap-2">
                    <i data-lucide="users" class="w-4 h-4 text-blue-500"></i>
                    <div>
                        <h3 class="font-semibold text-slate-800 dark:text-white">Pengajar Mapel</h3>
                        <p class="text-xs text-slate-400 dark:text-zinc-500" x-text="pengajarMapelNama"></p>
                    </div>
                </div>
                <button @click="pengajarModal = false"><i data-lucide="x" class="w-5 h-5 text-slate-400"></i></button>
            </div>
            <div class="overflow-y-auto flex-1">
                <template x-if="pengajarLoading">
                    <div class="flex items-center justify-center py-12">
                        <i data-lucide="loader-2" class="w-6 h-6 animate-spin text-slate-400"></i>
                    </div>
                </template>
                <template x-if="!pengajarLoading && pengajarList.length === 0">
                    <div class="flex flex-col items-center justify-center py-12 text-slate-400 dark:text-zinc-600">
                        <i data-lucide="user-x" class="w-10 h-10 mb-2 opacity-40"></i>
                        <p class="text-sm">Belum ada guru yang mengajar mapel ini</p>
                    </div>
                </template>
                <template x-if="!pengajarLoading && pengajarList.length > 0">
                    <div class="divide-y divide-slate-100 dark:divide-zinc-700/50">
                        <template x-for="(item, i) in pengajarList" :key="i">
                            <div class="px-6 py-4">
                                <div class="flex items-center gap-3 mb-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/40 flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold text-blue-600 dark:text-blue-400"
                                              x-text="item.guru.charAt(0).toUpperCase()"></span>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-200" x-text="item.guru"></span>
                                </div>
                                <div class="flex flex-wrap gap-1.5 pl-11">
                                    <template x-for="(kelas, ki) in item.kelas" :key="ki">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium"
                                              :class="kelas.is_aktif
                                                ? 'bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800/40'
                                                : 'bg-slate-100 dark:bg-zinc-700/60 text-slate-500 dark:text-zinc-400'">
                                            <i data-lucide="school" class="w-3 h-3 flex-shrink-0"></i>
                                            <span x-text="'Kelas ' + kelas.nama"></span>
                                            <span class="opacity-60" x-text="'· ' + kelas.tahun_ajaran"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
            <div class="px-6 py-3 border-t border-slate-100 dark:border-zinc-700/50 flex-shrink-0">
                <p class="text-xs text-slate-400 dark:text-zinc-500">
                    <span class="text-amber-500 font-semibold">■</span> Tahun ajaran aktif &nbsp;
                    <span class="text-slate-400 font-semibold">■</span> Tahun ajaran lainnya
                </p>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div x-show="importModal" x-transition.opacity
         x-effect="document.documentElement.style.overflow = importModal ? 'hidden' : ''; document.body.style.overflow = importModal ? 'hidden' : ''"
         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
         @click.self="importModal = false">
        <div x-show="importModal" x-transition.scale.95 @click.stop
             class="w-full max-w-md bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-zinc-700">
                <div class="flex items-center gap-2">
                    <i data-lucide="upload" class="w-4 h-4 text-amber-500"></i>
                    <h3 class="font-semibold text-slate-800 dark:text-white">Import Mata Pelajaran</h3>
                </div>
                <button @click="importModal = false"><i data-lucide="x" class="w-5 h-5 text-slate-400"></i></button>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-800/30 rounded-xl">
                    <div class="flex items-center gap-2 text-sm text-amber-700 dark:text-amber-400">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4 flex-shrink-0"></i>
                        <span>Unduh template Excel terlebih dahulu</span>
                    </div>
                    <a href="{{ route('admin.mapel.import.template') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-400 hover:bg-amber-500 text-zinc-900 font-semibold text-xs rounded-lg transition-colors">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i> Unduh
                    </a>
                </div>
                <div class="text-xs text-slate-500 dark:text-zinc-400 space-y-1 px-1">
                    <p class="font-semibold text-slate-600 dark:text-zinc-300">Kolom yang diperlukan:</p>
                    <p><span class="font-mono bg-slate-100 dark:bg-zinc-700 px-1 rounded">nama_mapel</span> — Nama mata pelajaran, maks. 100 karakter <span class="text-red-400">*wajib</span></p>
                    <p><span class="font-mono bg-slate-100 dark:bg-zinc-700 px-1 rounded">kode_mapel</span> — Kode unik, maks. 20 karakter <span class="text-red-400">*wajib, harus unik</span></p>
                    <p class="text-slate-400 italic pt-1">Contoh: Matematika/MTK, Bahasa Indonesia/BI, Fisika/FIS</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                        <i data-lucide="file-up" class="w-3.5 h-3.5 inline mr-1"></i>File Excel (.xlsx / .xls)
                    </label>
                    <input type="file" accept=".xlsx,.xls" @change="importFile = $event.target.files[0]"
                           class="input-field text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-50 dark:file:bg-amber-900/40 file:text-amber-700 dark:file:text-amber-400 hover:file:bg-amber-100">
                </div>
                <template x-if="importResult">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-sm font-medium"
                             :class="importResult.success_count > 0 ? 'text-green-600 dark:text-green-400' : 'text-slate-500 dark:text-zinc-400'">
                            <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                            <span x-text="`${importResult.success_count} mata pelajaran berhasil diimpor.`"></span>
                        </div>
                        <template x-if="importResult.errors && importResult.errors.length > 0">
                            <div class="max-h-40 overflow-y-auto space-y-1 p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800/30 rounded-xl">
                                <p class="text-xs font-semibold text-red-600 dark:text-red-400 mb-1">
                                    <span x-text="importResult.errors.length"></span> baris gagal:
                                </p>
                                <template x-for="err in importResult.errors" :key="err.row">
                                    <p class="text-xs text-red-500 dark:text-red-400">
                                        <span class="font-mono font-bold">Baris <span x-text="err.row"></span>:</span>
                                        <span x-text="err.message"></span>
                                    </p>
                                </template>
                            </div>
                        </template>
                    </div>
                </template>
                <p x-show="importErrorMsg" x-text="importErrorMsg" class="text-xs text-red-500"></p>
                <div class="flex gap-3 pt-1">
                    <button type="button" @click="importModal = false" class="btn-secondary flex-1">Tutup</button>
                    <button type="button" @click="submitImport()" :disabled="importLoading || !importFile" class="btn-primary flex-1">
                        <i data-lucide="upload" class="w-4 h-4" x-show="!importLoading"></i>
                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin" x-show="importLoading"></i>
                        <span x-text="importLoading ? 'Mengimpor...' : 'Import'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function mapelManager() {
    return {
        modal: false, mode: 'create', loading: false, errorMsg: '', errors: {},
        form: { nama: '', kode: '' }, editId: null,
        importModal: false, importFile: null, importLoading: false, importResult: null, importErrorMsg: '',
        pengajarModal: false, pengajarLoading: false, pengajarList: [], pengajarMapelNama: '',
        init() { this.$nextTick(() => lucide.createIcons()); },
        async openPengajar(id, nama) {
            this.pengajarMapelNama = nama;
            this.pengajarList = [];
            this.pengajarLoading = true;
            this.pengajarModal = true;
            this.$nextTick(() => lucide.createIcons());
            try {
                const res = await fetch(`/admin/mapel/${id}/pengajar`, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                this.pengajarList = data.pengajar || [];
            } catch { this.pengajarList = []; }
            finally { this.pengajarLoading = false; this.$nextTick(() => lucide.createIcons()); }
        },
        openCreate() { this.mode = 'create'; this.form = { nama: '', kode: '' }; this.errors = {}; this.errorMsg = ''; this.modal = true; this.$nextTick(() => lucide.createIcons()); },
        openEdit(item) {
            this.mode = 'edit'; this.editId = item.id;
            this.form = { nama: item.nama, kode: item.kode || '' };
            this.errors = {}; this.errorMsg = ''; this.modal = true;
            this.$nextTick(() => {
                lucide.createIcons();
                this.$root.querySelectorAll('select').forEach(el => {
                    if (el.tomselect) el.tomselect.setValue(el.value);
                });
            });
        },
        async submitForm() {
            this.loading = true; this.errors = {}; this.errorMsg = '';
            const url = this.mode === 'create' ? '{{ route('admin.mapel.store') }}' : `/admin/mapel/${this.editId}`;
            try {
                const res = await fetch(url, { method: this.mode === 'create' ? 'POST' : 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify(this.form) });
                const data = await res.json();
                if (!res.ok) { if (data.errors) this.errors = Object.fromEntries(Object.entries(data.errors).map(([k,v]) => [k,v[0]])); else this.errorMsg = data.message; }
                else { this.modal = false; Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' }); setTimeout(() => location.reload(), 1200); }
            } catch { this.errorMsg = 'Gagal terhubung.'; }
            finally { this.loading = false; }
        },
        async deleteMapel(id, nama) {
            const { isConfirmed } = await Swal.fire({ title: 'Hapus Mapel?', text: `Hapus "${nama}"?`, icon: 'warning', showCancelButton: true, confirmButtonText: 'Hapus', cancelButtonText: 'Batal', confirmButtonColor: '#ef4444' });
            if (!isConfirmed) return;
            const res = await fetch(`/admin/mapel/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } });
            const data = await res.json();
            Swal.fire({ icon: 'success', title: 'Dihapus!', text: data.message, timer: 1200, showConfirmButton: false, toast: true, position: 'top-end' });
            setTimeout(() => location.reload(), 1000);
        },

        openImport() {
            this.importFile = null; this.importResult = null; this.importErrorMsg = '';
            this.importModal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        async submitImport() {
            if (!this.importFile) return;
            this.importLoading = true; this.importResult = null; this.importErrorMsg = '';
            const formData = new FormData();
            formData.append('file', this.importFile);
            formData.append('_token', '{{ csrf_token() }}');
            try {
                const res = await fetch('{{ route('admin.mapel.import') }}', {
                    method: 'POST', headers: { 'Accept': 'application/json' }, body: formData
                });
                const data = await res.json();
                if (!res.ok) { this.importErrorMsg = data.errors?.file?.[0] || data.message || 'Terjadi kesalahan.'; }
                else {
                    this.importResult = data;
                    this.$nextTick(() => lucide.createIcons());
                    if (data.success_count > 0) setTimeout(() => location.reload(), 2500);
                }
            } catch { this.importErrorMsg = 'Gagal terhubung ke server.'; }
            finally { this.importLoading = false; this.$nextTick(() => lucide.createIcons()); }
        }
    }
}
</script>
@endpush
