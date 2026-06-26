@extends('layouts.app')
@section('title', 'Tahun Ajaran')
@section('page-title', 'Tahun Ajaran')
@section('content')
<div x-data="taManager()" x-init="init()">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">Tahun Ajaran</h2>
            <p class="text-sm text-slate-400 dark:text-zinc-500">Kelola periode akademik</p>
        </div>
        <button @click="openCreate()" class="btn-primary">
            <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah
        </button>
    </div>

    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 dark:bg-zinc-800/60 border-b border-slate-200 dark:border-zinc-700/50">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Semester</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-zinc-700/50">
                @forelse($tahunAjaran as $ta)
                <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                    <td class="px-4 py-3.5 font-semibold text-slate-700 dark:text-slate-200">{{ $ta->nama }}</td>
                    <td class="px-4 py-3.5">
                        <span class="badge {{ $ta->semester === 'Ganjil' ? 'bg-blue-100 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400' : 'bg-purple-100 dark:bg-purple-950/40 text-purple-700 dark:text-purple-400' }}">
                            {{ $ta->semester }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        @if($ta->is_aktif)
                            <span class="badge badge-validated">Aktif</span>
                        @else
                            <span class="badge badge-draft">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center justify-end gap-1.5">
                            @if(!$ta->is_aktif)
                            <button @click="aktivasi({{ $ta->id }}, '{{ $ta->nama }} {{ $ta->semester }}')"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-950/30 rounded-lg transition-colors">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Aktifkan
                            </button>
                            @endif
                            <button @click="openEdit({{ $ta->toJson() }})" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30 rounded-lg transition-colors">
                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                            </button>
                            @if(!$ta->is_aktif)
                            <button @click="deleteTA({{ $ta->id }})" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-colors">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-12 text-center text-slate-400"><i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2 opacity-50"></i><p class="text-sm">Belum ada tahun ajaran</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div x-show="modal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="modal = false">
        <div x-show="modal" x-transition.scale.95 class="w-full max-w-sm bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-zinc-700">
                <div class="flex items-center gap-2"><i data-lucide="calendar-range" class="w-4 h-4 text-amber-500"></i>
                    <h3 class="font-semibold text-slate-800 dark:text-white" x-text="mode === 'create' ? 'Tambah Tahun Ajaran' : 'Edit Tahun Ajaran'"></h3></div>
                <button @click="modal = false"><i data-lucide="x" class="w-5 h-5 text-slate-400"></i></button>
            </div>
            <form @submit.prevent="submitForm()" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">Nama (Tahun)</label>
                    <input type="text" x-model="form.nama" class="input-field" placeholder="2025/2026" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">Semester</label>
                    <select x-model="form.semester" class="input-field" required>
                        <option value="">Pilih Semester</option>
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                </div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" x-model="form.is_aktif" class="w-4 h-4 rounded border-slate-300 text-amber-500 focus:ring-amber-400">
                    <span class="text-sm text-slate-600 dark:text-zinc-400">Set sebagai aktif</span>
                </label>
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
function taManager() {
    return {
        modal: false, mode: 'create', loading: false, errorMsg: '', editId: null,
        form: { nama: '', semester: '', is_aktif: false },
        init() { this.$nextTick(() => lucide.createIcons()); },
        openCreate() { this.mode = 'create'; this.form = { nama: '', semester: '', is_aktif: false }; this.errorMsg = ''; this.modal = true; this.$nextTick(() => lucide.createIcons()); },
        openEdit(item) { this.mode = 'edit'; this.editId = item.id; this.form = { nama: item.nama, semester: item.semester, is_aktif: !!item.is_aktif }; this.errorMsg = ''; this.modal = true; this.$nextTick(() => lucide.createIcons()); },
        async submitForm() {
            this.loading = true; this.errorMsg = '';
            const url = this.mode === 'create' ? '{{ route('admin.tahun-ajaran.store') }}' : `/admin/tahun-ajaran/${this.editId}`;
            try {
                const res = await fetch(url, { method: this.mode === 'create' ? 'POST' : 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify(this.form) });
                const data = await res.json();
                if (!res.ok) { this.errorMsg = data.message || 'Error'; }
                else { this.modal = false; Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' }); setTimeout(() => location.reload(), 1200); }
            } catch { this.errorMsg = 'Gagal.'; }
            finally { this.loading = false; }
        },
        async aktivasi(id, label) {
            const { isConfirmed } = await Swal.fire({ title: 'Aktifkan?', text: `Set "${label}" sebagai tahun ajaran aktif?`, icon: 'question', showCancelButton: true, confirmButtonText: 'Aktifkan', cancelButtonText: 'Batal', confirmButtonColor: '#22c55e' });
            if (!isConfirmed) return;
            const res = await fetch(`/admin/tahun-ajaran/${id}/aktivasi`, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } });
            const data = await res.json();
            Swal.fire({ icon: 'success', title: 'Diaktifkan!', text: data.message, timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' });
            setTimeout(() => location.reload(), 1200);
        },
        async deleteTA(id) {
            const { isConfirmed } = await Swal.fire({ title: 'Hapus?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Hapus', cancelButtonText: 'Batal', confirmButtonColor: '#ef4444' });
            if (!isConfirmed) return;
            const res = await fetch(`/admin/tahun-ajaran/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } });
            const data = await res.json();
            if (res.ok) { Swal.fire({ icon: 'success', title: 'Dihapus!', text: data.message, timer: 1200, showConfirmButton: false, toast: true, position: 'top-end' }); setTimeout(() => location.reload(), 1000); }
            else { Swal.fire({ icon: 'error', title: 'Gagal', text: data.message }); }
        }
    }
}
</script>
@endpush
