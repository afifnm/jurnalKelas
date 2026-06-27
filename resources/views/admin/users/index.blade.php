@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Pengguna')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Admin</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i><span class="text-slate-700 dark:text-zinc-200 font-medium">Pengguna</span>
@endsection

@section('content')
<div x-data="userManager()" x-init="init()">

    <!-- Header -->
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">Daftar Pengguna</h2>
            <p class="text-sm text-slate-400 dark:text-zinc-500">Kelola akun admin, guru, dan kepala sekolah</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="openImport()" class="btn-secondary">
                <i data-lucide="upload" class="w-4 h-4"></i> Import
            </button>
            <button @click="openCreate()" class="btn-primary">
                <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Pengguna
            </button>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="flex items-center gap-2 mb-4">
        <div class="relative shrink-0">
            <i data-lucide="search" class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                style="width:220px" class="input-field py-1.5 pl-8 text-sm">
        </div>
        <select name="role" style="width:auto" class="input-field py-1.5 text-sm shrink-0">
            <option value="">Semua Role</option>
            @foreach($roles as $r)
                <option value="{{ $r }}" @selected(request('role') === $r)>{{ ucfirst($r) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary py-1.5 text-sm shrink-0">
            <i data-lucide="search" class="w-3.5 h-3.5"></i> Cari
        </button>
        @if(request()->hasAny(['search', 'role']))
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1 py-1.5 px-3 text-sm rounded-lg border border-slate-200 dark:border-zinc-700 text-slate-500 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors shrink-0">
            <i data-lucide="x" class="w-3.5 h-3.5"></i> Reset
        </a>
        @endif
    </form>

    <!-- Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-zinc-800/60 border-b border-slate-200 dark:border-zinc-700/50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider w-10">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Pengguna</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Kode Guru</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">No HP</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-700/50">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors {{ $user->trashed() ? 'opacity-50' : '' }}">
                        <td class="px-4 py-3.5 text-slate-400 dark:text-zinc-500 text-xs">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-yellow-400 to-yellow-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
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
                                    <button @click="resetPassword({{ $user->id }}, '{{ addslashes($user->nama) }}')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 rounded-lg transition-colors">
                                        <i data-lucide="key-round" class="w-3.5 h-3.5"></i> Reset
                                    </button>
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
                        <td colspan="7" class="px-4 py-12 text-center">
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
         x-effect="document.documentElement.style.overflow = modal ? 'hidden' : ''; document.body.style.overflow = modal ? 'hidden' : ''"
         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
         @click.self="modal = false">
        <div x-show="modal" x-transition.scale.95 @click.stop
             class="w-full max-w-lg bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl flex flex-col max-h-[90vh]">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-zinc-700 flex-shrink-0">
                <div class="flex items-center gap-2">
                    <i :data-lucide="mode === 'create' ? 'user-plus' : 'user-pen'" class="w-4 h-4 text-amber-500"></i>
                    <h3 class="font-semibold text-slate-800 dark:text-white" x-text="mode === 'create' ? 'Tambah Pengguna' : 'Edit Pengguna'"></h3>
                </div>
                <button @click="modal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form @submit.prevent="submitForm()" class="overflow-y-auto p-6 space-y-4">
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
                            <i data-lucide="at-sign" class="w-3.5 h-3.5 inline mr-1"></i>Kode Guru
                        </label>
                        <input type="text" x-model="form.username" class="input-field" placeholder="kode guru" required>
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
                    <h3 class="font-semibold text-slate-800 dark:text-white">Import Pengguna (Guru)</h3>
                </div>
                <button @click="importModal = false"><i data-lucide="x" class="w-5 h-5 text-slate-400"></i></button>
            </div>
            <div class="p-6 space-y-4">
                <!-- Template download -->
                <div class="flex items-center justify-between p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-800/30 rounded-xl">
                    <div class="flex items-center gap-2 text-sm text-amber-700 dark:text-amber-400">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4 flex-shrink-0"></i>
                        <span>Unduh template Excel terlebih dahulu</span>
                    </div>
                    <a href="{{ route('admin.users.import.template') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-400 hover:bg-amber-500 text-zinc-900 font-semibold text-xs rounded-lg transition-colors">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i> Unduh
                    </a>
                </div>
                <!-- Info kolom -->
                <div class="text-xs text-slate-500 dark:text-zinc-400 space-y-1 px-1">
                    <p class="font-semibold text-slate-600 dark:text-zinc-300">Kolom yang diperlukan:</p>
                    <p><span class="font-mono bg-slate-100 dark:bg-zinc-700 px-1 rounded">nama</span> — Nama lengkap guru <span class="text-red-400">*wajib</span></p>
                    <p><span class="font-mono bg-slate-100 dark:bg-zinc-700 px-1 rounded">kode_guru</span> — Kode unik guru <span class="text-red-400">*wajib</span></p>
                    <p><span class="font-mono bg-slate-100 dark:bg-zinc-700 px-1 rounded">email</span> — Email <span class="text-slate-400">opsional</span></p>
                    <p><span class="font-mono bg-slate-100 dark:bg-zinc-700 px-1 rounded">telp</span> — No. HP <span class="text-slate-400">opsional</span></p>
                    <p class="text-slate-400 italic pt-1">Password otomatis: <strong class="font-mono">12345678</strong> &nbsp;|&nbsp; Role otomatis: <strong>Guru</strong></p>
                </div>
                <!-- File input -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                        <i data-lucide="file-up" class="w-3.5 h-3.5 inline mr-1"></i>File Excel (.xlsx / .xls)
                    </label>
                    <input type="file" accept=".xlsx,.xls" @change="importFile = $event.target.files[0]"
                           class="input-field text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-50 dark:file:bg-amber-900/40 file:text-amber-700 dark:file:text-amber-400 hover:file:bg-amber-100">
                </div>
                <!-- Result -->
                <template x-if="importResult">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-sm font-medium"
                             :class="importResult.success_count > 0 ? 'text-green-600 dark:text-green-400' : 'text-slate-500 dark:text-zinc-400'">
                            <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                            <span x-text="`${importResult.success_count} pengguna berhasil diimpor.`"></span>
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
function userManager() {
    return {
        modal: false, mode: 'create', loading: false,
        showPass: false,
        form: { nama: '', username: '', email: '', password: '', role: '', no_hp: '', is_active: true },
        errors: {}, errorMsg: '', editId: null,
        importModal: false, importFile: null, importLoading: false, importResult: null, importErrorMsg: '',

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

        async resetPassword(id, nama) {
            const { isConfirmed } = await Swal.fire({
                title: 'Reset Password?',
                html: `Password <strong>${nama}</strong> akan direset ke <code>12345678</code>.`,
                icon: 'warning', showCancelButton: true,
                confirmButtonText: 'Ya, Reset', cancelButtonText: 'Batal',
                confirmButtonColor: '#6366f1',
            });
            if (!isConfirmed) return;
            try {
                const res = await fetch(`/admin/users/${id}/reset-password`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (res.ok) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 2500, showConfirmButton: false, toast: true, position: 'top-end' });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: data.message || 'Terjadi kesalahan.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                }
            } catch {
                Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Tidak dapat terhubung ke server.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
            }
        },

        async restoreUser(id) {
            const res = await fetch(`/admin/users/${id}/restore`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } });
            const data = await res.json();
            Swal.fire({ icon: 'success', title: 'Dipulihkan!', text: data.message, timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' });
            setTimeout(() => location.reload(), 1200);
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
                const res = await fetch('{{ route('admin.users.import') }}', {
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
