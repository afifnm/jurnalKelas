@extends('layouts.app')
@section('title', 'Manajemen Kelas')
@section('page-title', 'Kelas')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Admin</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i><span>Kelas</span>
@endsection

@section('content')
<div x-data="kelasManager()" x-init="init()">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">Daftar Kelas</h2>
            <p class="text-sm text-slate-400 dark:text-zinc-500">Kelola data kelas yang tersedia</p>
        </div>
        <button @click="openCreate()" class="btn-primary">
            <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Kelas
        </button>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
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
                                <a href="{{ route('admin.jadwal.by-kelas', ['kelas_id' => $item->id]) }}"
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
    <div x-show="modal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="modal = false">
        <div x-show="modal" x-transition.scale.95 class="w-full max-w-sm bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden">
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
</div>
@endsection

@push('scripts')
<script>
function kelasManager() {
    return {
        modal: false, mode: 'create', loading: false, errorMsg: '', nama: '', editId: null,
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
        }
    }
}
</script>
@endpush
