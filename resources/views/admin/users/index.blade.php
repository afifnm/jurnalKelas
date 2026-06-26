@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Pengguna')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Admin</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i><span>Pengguna</span>
@endsection

@section('content')
<div x-data="userManager()" x-init="init()">

    <!-- Header -->
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">Daftar Pengguna</h2>
            <p class="text-sm text-slate-400 dark:text-zinc-500">Kelola akun admin, guru, dan kepala sekolah</p>
        </div>
        <button @click="openCreate()" class="btn-primary">
            <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Pengguna
        </button>
    </div>

    <!-- Filters -->
    <form method="GET" class="flex items-center gap-2 mb-4">
        <div class="relative flex-1">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                class="input-field pl-9 text-sm" placeholder="Cari nama atau username...">
        </div>
        <select name="role" class="input-field w-36 text-sm flex-shrink-0">
            <option value="">Semua Role</option>
            @foreach($roles as $r)
                <option value="{{ $r }}" @selected(request('role') === $r)>{{ ucfirst($r) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary text-sm flex-shrink-0">
            <i data-lucide="filter" class="w-4 h-4"></i> Filter
        </button>
        @if(request()->hasAny(['search', 'role']))
        <a href="{{ route('admin.users.index') }}" class="btn-secondary text-sm flex-shrink-0">
            <i data-lucide="x" class="w-4 h-4"></i>
        </a>
        @endif
    </form>

    <!-- Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-zinc-800/60 border-b border-slate-200 dark:border-zinc-700/50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Pengguna</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Username</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">No HP</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-700/50">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors {{ $user->trashed() ? 'opacity-50' : '' }}">
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-400 to-orange-400 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                    {{ strtoupper(substr($user->nama, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-700 dark:text-slate-200">{{ $user->nama }}</p>
                                    <p class="text-xs text-slate-400 dark:text-zinc-500">{{ $user->email ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <code class="text-xs bg-slate-100 dark:bg-zinc-700 px-2 py-0.5 rounded text-slate-600 dark:text-zinc-300">{{ $user->username }}</code>
                        </td>
                        <td class="px-4 py-3.5">
                            @php $role = $user->roles->first()?->name ?? '-' @endphp
                            <span class="badge {{ match($role) { 'admin' => 'bg-red-100 dark:bg-red-950/40 text-red-700 dark:text-red-400', 'ks' => 'bg-purple-100 dark:bg-purple-950/40 text-purple-700 dark:text-purple-400', 'guru' => 'bg-blue-100 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400', default => 'bg-slate-100 text-slate-600' } }}">
                                {{ ucfirst($role) }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-slate-600 dark:text-zinc-400 text-xs">{{ $user->no_hp ?? '-' }}</td>
                        <td class="px-4 py-3.5">
                            @if($user->trashed())
                                <span class="badge bg-red-100 dark:bg-red-950/40 text-red-600 dark:text-red-400">Dihapus</span>
                            @elseif($user->is_active)
                                <span class="badge bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-400">Aktif</span>
                            @else
                                <span class="badge bg-slate-100 dark:bg-zinc-700 text-slate-500 dark:text-zinc-400">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end gap-2">
                                @if($user->trashed())
                                    <button @click="restoreUser({{ $user->id }})"
                                        class="text-xs text-green-600 dark:text-green-400 hover:underline">Pulihkan</button>
                                @else
                                    @if(($user->roles->first()?->name ?? '') === 'guru')
                                    <a href="{{ route('admin.jadwal.by-guru', ['guru_id' => $user->id]) }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/30 rounded-lg transition-colors">
                                        <i data-lucide="calendar-clock" class="w-3.5 h-3.5"></i> Jadwal
                                    </a>
                                    @endif
                                    <button @click="openEdit({{ $user->toJson() }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30 rounded-lg transition-colors">
                                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                                    </button>
                                    @if($user->id !== auth()->id())
                                    <button @click="deleteUser({{ $user->id }}, '{{ addslashes($user->nama) }}')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-colors">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                    </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center text-slate-400 dark:text-zinc-600">
                                <i data-lucide="users-x" class="w-10 h-10 mb-2 opacity-50"></i>
                                <p class="text-sm">Tidak ada pengguna ditemukan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-4 py-3 border-t border-slate-100 dark:border-zinc-700/50">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Create/Edit -->
    <div x-show="modal" x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="modal = false">
        <div x-show="modal" x-transition.scale.95
             class="w-full max-w-lg bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-zinc-700">
                <div class="flex items-center gap-2">
                    <i :data-lucide="mode === 'create' ? 'user-plus' : 'user-pen'" class="w-4 h-4 text-amber-500"></i>
                    <h3 class="font-semibold text-slate-800 dark:text-white" x-text="mode === 'create' ? 'Tambah Pengguna' : 'Edit Pengguna'"></h3>
                </div>
                <button @click="modal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form @submit.prevent="submitForm()" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                            <i data-lucide="user" class="w-3.5 h-3.5 inline mr-1"></i>Nama Lengkap
                        </label>
                        <input type="text" x-model="form.nama" class="input-field" placeholder="Nama lengkap" required>
                        <p x-show="errors.nama" x-text="errors.nama" class="text-xs text-red-500 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                            <i data-lucide="at-sign" class="w-3.5 h-3.5 inline mr-1"></i>Username
                        </label>
                        <input type="text" x-model="form.username" class="input-field" placeholder="username" required>
                        <p x-show="errors.username" x-text="errors.username" class="text-xs text-red-500 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                            <i data-lucide="shield" class="w-3.5 h-3.5 inline mr-1"></i>Role
                        </label>
                        <select x-model="form.role" class="input-field" required>
                            <option value="">Pilih role</option>
                            <option value="admin">Admin</option>
                            <option value="guru">Guru</option>
                            <option value="ks">Kepala Sekolah</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                            <i data-lucide="mail" class="w-3.5 h-3.5 inline mr-1"></i>Email <span class="text-slate-400">(opsional)</span>
                        </label>
                        <input type="email" x-model="form.email" class="input-field" placeholder="email@sekolah.id">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                            <i data-lucide="phone" class="w-3.5 h-3.5 inline mr-1"></i>No HP
                        </label>
                        <input type="text" x-model="form.no_hp" class="input-field" placeholder="08xx">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                            <i data-lucide="lock" class="w-3.5 h-3.5 inline mr-1"></i>
                            Password <span x-show="mode === 'edit'" class="text-slate-400">(kosongkan jika tidak diubah)</span>
                        </label>
                        <input :type="showPass ? 'text' : 'password'" x-model="form.password" class="input-field"
                            :placeholder="mode === 'create' ? 'Minimal 6 karakter' : 'Kosongkan jika tidak diubah'"
                            :required="mode === 'create'">
                    </div>
                    <div class="col-span-2 flex items-center gap-2">
                        <input type="checkbox" x-model="form.is_active" id="is_active_check" class="w-4 h-4 rounded border-slate-300 text-amber-500 focus:ring-amber-400">
                        <label for="is_active_check" class="text-sm text-slate-600 dark:text-zinc-400">Akun aktif</label>
                    </div>
                </div>

                <div x-show="errorMsg" class="p-3 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/40 rounded-lg text-sm text-red-600 dark:text-red-400" x-text="errorMsg"></div>

                <div class="flex gap-3 pt-2">
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
function userManager() {
    return {
        modal: false, mode: 'create', loading: false,
        showPass: false,
        form: { nama: '', username: '', email: '', password: '', role: '', no_hp: '', is_active: true },
        errors: {}, errorMsg: '', editId: null,

        init() { this.$nextTick(() => lucide.createIcons()); },

        openCreate() {
            this.mode = 'create';
            this.form = { nama: '', username: '', email: '', password: '', role: 'guru', no_hp: '', is_active: true };
            this.errors = {}; this.errorMsg = '';
            this.modal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        openEdit(user) {
            this.mode = 'edit'; this.editId = user.id;
            this.form = {
                nama: user.nama, username: user.username, email: user.email || '',
                password: '', role: user.roles?.[0]?.name || '',
                no_hp: user.no_hp || '', is_active: !!user.is_active
            };
            this.errors = {}; this.errorMsg = '';
            this.modal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        async submitForm() {
            this.loading = true; this.errors = {}; this.errorMsg = '';
            const url  = this.mode === 'create' ? '{{ route('admin.users.store') }}' : `/admin/users/${this.editId}`;
            const method = this.mode === 'create' ? 'POST' : 'PUT';
            try {
                const res = await fetch(url, {
                    method,
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify(this.form)
                });
                const data = await res.json();
                if (!res.ok) {
                    if (data.errors) this.errors = Object.fromEntries(Object.entries(data.errors).map(([k,v]) => [k, v[0]]));
                    else this.errorMsg = data.message || 'Terjadi kesalahan.';
                } else {
                    this.modal = false;
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1800, showConfirmButton: false, toast: true, position: 'top-end' });
                    setTimeout(() => location.reload(), 1500);
                }
            } catch { this.errorMsg = 'Gagal terhubung ke server.'; }
            finally { this.loading = false; }
        },

        async deleteUser(id, nama) {
            const { isConfirmed } = await Swal.fire({
                title: 'Hapus Pengguna?', text: `Hapus akun "${nama}"?`,
                icon: 'warning', showCancelButton: true,
                confirmButtonText: 'Hapus', cancelButtonText: 'Batal',
                confirmButtonColor: '#ef4444',
            });
            if (!isConfirmed) return;
            const res = await fetch(`/admin/users/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } });
            const data = await res.json();
            Swal.fire({ icon: 'success', title: 'Dihapus!', text: data.message, timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' });
            setTimeout(() => location.reload(), 1200);
        },

        async restoreUser(id) {
            const res = await fetch(`/admin/users/${id}/restore`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } });
            const data = await res.json();
            Swal.fire({ icon: 'success', title: 'Dipulihkan!', text: data.message, timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' });
            setTimeout(() => location.reload(), 1200);
        }
    }
}
</script>
@endpush
