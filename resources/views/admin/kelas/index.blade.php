@extends('layouts.app')
@section('title', 'Manajemen Kelas')
@section('page-title', 'Kelas')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Admin</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i><span class="text-slate-700 dark:text-zinc-200 font-medium">Kelas</span>
@endsection

@section('content')
<div x-data="kelasManager()" x-init="init()">
    <div class="flex items-start justify-between gap-3 mb-5">
        <div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">Daftar Kelas</h2>
            <p class="text-sm text-slate-400 dark:text-zinc-500">Kelola data kelas yang tersedia</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <button @click="openImport()" class="btn-secondary">
                <i data-lucide="upload" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Import</span>
            </button>
            <button @click="openCreate()" class="btn-primary">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Tambah Kelas</span>
            </button>
        </div>
    </div>

    <div class="card overflow-hidden">

        {{-- Mobile: Card List --}}
        <div class="divide-y divide-slate-100 dark:divide-zinc-700/50 md:hidden">
            @forelse($kelas as $item)
            <div class="flex items-center gap-3 p-4">
                <div class="w-9 h-9 rounded-lg bg-purple-50 dark:bg-purple-950/40 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="school" class="w-4 h-4 text-purple-600 dark:text-purple-400"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-700 dark:text-slate-200 text-sm">{{ $item->nama }}</p>
                    <span class="badge bg-slate-100 dark:bg-zinc-700 text-slate-600 dark:text-zinc-300 mt-0.5">{{ $item->jadwal_count }} jadwal</span>
                </div>
                <div class="flex items-center gap-1 flex-shrink-0">
                    <a href="{{ route('admin.jadwal.index', ['kelas_id' => $item->id]) }}"
                        class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/30 rounded-lg transition-colors">
                        <i data-lucide="calendar-clock" class="w-3.5 h-3.5"></i>
                    </a>
                    <button @click="openEdit({{ $item->id }}, '{{ addslashes($item->nama) }}')"
                        class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30 rounded-lg transition-colors">
                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                    </button>
                    <button @click="deleteKelas({{ $item->id }}, '{{ addslashes($item->nama) }}')"
                        class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-colors">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            </div>
            @empty
            <div class="px-4 py-12 text-center text-slate-400 dark:text-zinc-600">
                <div class="flex flex-col items-center">
                    <i data-lucide="inbox" class="w-10 h-10 mb-2 opacity-50"></i>
                    <p class="text-sm">Belum ada kelas</p>
                </div>
            </div>
            @endforelse
        </div>

        {{-- Desktop: Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-zinc-800/60 border-b border-slate-200 dark:border-zinc-700/50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider w-12">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Nama Kelas</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Jadwal</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-700/50">
                    @forelse($kelas as $item)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="px-4 py-3.5 text-slate-400 dark:text-zinc-500 text-xs">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-950/40 flex items-center justify-center">
                                    <i data-lucide="school" class="w-4 h-4 text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $item->nama }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="badge bg-slate-100 dark:bg-zinc-700 text-slate-600 dark:text-zinc-300">
                                {{ $item->jadwal_count }} jadwal
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.jadwal.index', ['kelas_id' => $item->id]) }}"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/30 rounded-lg transition-colors">
                                    <i data-lucide="calendar-clock" class="w-3.5 h-3.5"></i> Jadwal
                                </a>
                                <button @click="openEdit({{ $item->id }}, '{{ addslashes($item->nama) }}')"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30 rounded-lg transition-colors">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                                </button>
                                <button @click="deleteKelas({{ $item->id }}, '{{ addslashes($item->nama) }}')"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-colors">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-12 text-center text-slate-400 dark:text-zinc-600">
                        <div class="flex flex-col items-center">
                            <i data-lucide="inbox" class="w-10 h-10 mb-2 opacity-50"></i>
                            <p class="text-sm">Belum ada kelas</p>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kelas->hasPages())
        <div class="px-4 py-3 border-t border-slate-100 dark:border-zinc-700/50">{{ $kelas->links() }}</div>
        @endif
    </div>

    <!-- Modal -->
    <div x-show="modal" x-transition.opacity
         x-effect="document.documentElement.style.overflow = modal ? 'hidden' : ''; document.body.style.overflow = modal ? 'hidden' : ''"
         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
         @click.self="modal = false">
        <div x-show="modal" x-transition.scale.95 @click.stop class="w-full max-w-sm bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-zinc-700">
                <div class="flex items-center gap-2">
                    <i data-lucide="school" class="w-4 h-4 text-amber-500"></i>
                    <h3 class="font-semibold text-slate-800 dark:text-white" x-text="mode === 'create' ? 'Tambah Kelas' : 'Edit Kelas'"></h3>
                </div>
                <button @click="modal = false"><i data-lucide="x" class="w-5 h-5 text-slate-400"></i></button>
            </div>
            <form @submit.prevent="submitForm()" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                        <i data-lucide="school" class="w-3.5 h-3.5 inline mr-1"></i>Nama Kelas
                    </label>
                    <input type="text" x-model="nama" class="input-field" placeholder="Contoh: X RA" required>
                    <p x-show="errorMsg" x-text="errorMsg" class="text-xs text-red-500 mt-1"></p>
                </div>
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
                    <h3 class="font-semibold text-slate-800 dark:text-white">Import Kelas</h3>
                </div>
                <button @click="importModal = false"><i data-lucide="x" class="w-5 h-5 text-slate-400"></i></button>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-800/30 rounded-xl">
                    <div class="flex items-center gap-2 text-sm text-amber-700 dark:text-amber-400">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4 flex-shrink-0"></i>
                        <span>Unduh template Excel terlebih dahulu</span>
                    </div>
                    <a href="{{ route('admin.kelas.import.template') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-400 hover:bg-amber-500 text-zinc-900 font-semibold text-xs rounded-lg transition-colors">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i> Unduh
                    </a>
                </div>
                <div class="text-xs text-slate-500 dark:text-zinc-400 space-y-1 px-1">
                    <p class="font-semibold text-slate-600 dark:text-zinc-300">Kolom yang diperlukan:</p>
                    <p><span class="font-mono bg-slate-100 dark:bg-zinc-700 px-1 rounded">nama_kelas</span> — Nama kelas, maks. 50 karakter <span class="text-red-400">*wajib, harus unik</span></p>
                    <p class="text-slate-400 italic pt-1">Contoh: X RA, X RB, XI RA, XII RPL</p>
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
                            <span x-text="`${importResult.success_count} kelas berhasil diimpor.`"></span>
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
function kelasManager() {
    return {
        modal: false, mode: 'create', loading: false, errorMsg: '', nama: '', editId: null,
        importModal: false, importFile: null, importLoading: false, importResult: null, importErrorMsg: '',
        init() { this.$nextTick(() => lucide.createIcons()); },
        openCreate() { this.mode = 'create'; this.nama = ''; this.errorMsg = ''; this.modal = true; this.$nextTick(() => lucide.createIcons()); },
        openEdit(id, nama) { this.mode = 'edit'; this.editId = id; this.nama = nama; this.errorMsg = ''; this.modal = true; this.$nextTick(() => lucide.createIcons()); },
        async submitForm() {
            this.loading = true; this.errorMsg = '';
            const url = this.mode === 'create' ? '{{ route('admin.kelas.store') }}' : `/admin/kelas/${this.editId}`;
            const method = this.mode === 'create' ? 'POST' : 'PUT';
            try {
                const res = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify({ nama: this.nama }) });
                const data = await res.json();
                if (!res.ok) { this.errorMsg = data.errors?.nama?.[0] || data.message || 'Error'; }
                else { this.modal = false; Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' }); setTimeout(() => location.reload(), 1200); }
            } catch { this.errorMsg = 'Gagal terhubung.'; }
            finally { this.loading = false; }
        },
        async deleteKelas(id, nama) {
            const { isConfirmed } = await Swal.fire({ title: 'Hapus Kelas?', text: `Hapus "${nama}"?`, icon: 'warning', showCancelButton: true, confirmButtonText: 'Hapus', cancelButtonText: 'Batal', confirmButtonColor: '#ef4444' });
            if (!isConfirmed) return;
            const res = await fetch(`/admin/kelas/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } });
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
                const res = await fetch('{{ route('admin.kelas.import') }}', {
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
