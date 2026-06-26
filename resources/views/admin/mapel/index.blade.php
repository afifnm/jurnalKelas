@extends('layouts.app')
@section('title', 'Mata Pelajaran')
@section('page-title', 'Mata Pelajaran')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Admin</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i><span>Mata Pelajaran</span>
@endsection

@section('content')
<div x-data="mapelManager()" x-init="init()">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">Mata Pelajaran</h2>
            <p class="text-sm text-slate-400 dark:text-zinc-500">Kelola daftar mata pelajaran</p>
        </div>
        <button @click="openCreate()" class="btn-primary">
            <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Mapel
        </button>
    </div>

    <div class="card overflow-hidden">
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
        @if($mapel->hasPages())
        <div class="px-4 py-3 border-t border-slate-100 dark:border-zinc-700/50">{{ $mapel->links() }}</div>
        @endif
    </div>

    <!-- Modal -->
    <div x-show="modal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="modal = false">
        <div x-show="modal" x-transition.scale.95 class="w-full max-w-sm bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden">
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
</div>
@endsection

@push('scripts')
<script>
function mapelManager() {
    return {
        modal: false, mode: 'create', loading: false, errorMsg: '', errors: {},
        form: { nama: '', kode: '' }, editId: null,
        init() { this.$nextTick(() => lucide.createIcons()); },
        openCreate() { this.mode = 'create'; this.form = { nama: '', kode: '' }; this.errors = {}; this.errorMsg = ''; this.modal = true; this.$nextTick(() => lucide.createIcons()); },
        openEdit(item) { this.mode = 'edit'; this.editId = item.id; this.form = { nama: item.nama, kode: item.kode || '' }; this.errors = {}; this.errorMsg = ''; this.modal = true; this.$nextTick(() => lucide.createIcons()); },
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
        }
    }
}
</script>
@endpush
