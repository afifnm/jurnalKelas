@extends('layouts.app')
@section('title', 'Jadwal Mengajar')
@section('page-title', 'Jadwal Mengajar')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Admin</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i><span>Jadwal</span>
@endsection

@section('content')
<div x-data="jadwalManager()" x-init="init()">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">Jadwal Mengajar</h2>
            <p class="text-sm text-slate-400 dark:text-zinc-500">Sumber kebenaran untuk validasi kepatuhan guru</p>
        </div>
        <button @click="openCreate()" class="btn-primary">
            <i data-lucide="calendar-plus" class="w-4 h-4"></i> Tambah Jadwal
        </button>
    </div>

    <!-- Filter -->
    <form method="GET" class="flex items-center gap-2 mb-4">
        <select name="guru_id" class="input-field w-48 text-sm flex-shrink-0">
            <option value="">Semua Guru</option>
            @foreach($guru as $g)
                <option value="{{ $g->id }}" @selected(request('guru_id') == $g->id)>{{ $g->nama }}</option>
            @endforeach
        </select>
        <select name="tahun_ajaran_id" class="input-field w-52 text-sm flex-shrink-0">
            <option value="">Semua Tahun Ajaran</option>
            @foreach($tahunAjaran as $ta)
                <option value="{{ $ta->id }}" @selected(request('tahun_ajaran_id') == $ta->id)>{{ $ta->nama }} - {{ $ta->semester }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn-primary text-sm flex-shrink-0">
            <i data-lucide="filter" class="w-4 h-4"></i> Filter
        </button>
        @if(request()->hasAny(['guru_id', 'tahun_ajaran_id']))
        <a href="{{ route('admin.jadwal.index') }}" class="btn-secondary text-sm flex-shrink-0">
            <i data-lucide="x" class="w-4 h-4"></i>
        </a>
        @endif
    </form>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-zinc-800/60 border-b border-slate-200 dark:border-zinc-700/50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Guru</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Kelas</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Mapel</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Hari</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Waktu</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-700/50">
                    @forelse($jadwal as $item)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center text-amber-600 dark:text-amber-400 text-xs font-bold">
                                    {{ strtoupper(substr($item->guru->nama, 0, 1)) }}
                                </div>
                                <span class="text-sm text-slate-700 dark:text-slate-200 font-medium">{{ $item->guru->nama }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5"><span class="badge bg-purple-100 dark:bg-purple-950/40 text-purple-700 dark:text-purple-400">{{ $item->kelas->nama }}</span></td>
                        <td class="px-4 py-3.5 text-slate-600 dark:text-zinc-400">{{ $item->mapel->nama }}</td>
                        <td class="px-4 py-3.5">
                            <span class="badge bg-blue-100 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400">{{ $item->nama_hari }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-sm font-mono text-slate-600 dark:text-zinc-400">{{ substr($item->jam_mulai,0,5) }} – {{ substr($item->jam_selesai,0,5) }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                <button @click="openEdit({{ $item->toJson() }})" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30 rounded-lg transition-colors">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                                </button>
                                <button @click="deleteJadwal({{ $item->id }})" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-colors">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-slate-400 dark:text-zinc-600">
                        <div class="flex flex-col items-center"><i data-lucide="calendar-x-2" class="w-10 h-10 mb-2 opacity-50"></i><p class="text-sm">Belum ada jadwal</p></div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($jadwal->hasPages())
        <div class="px-4 py-3 border-t border-slate-100 dark:border-zinc-700/50">{{ $jadwal->links() }}</div>
        @endif
    </div>

    <!-- Modal -->
    <div x-show="modal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="modal = false">
        <div x-show="modal" x-transition.scale.95 class="w-full max-w-md bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-zinc-700">
                <div class="flex items-center gap-2"><i data-lucide="calendar-clock" class="w-4 h-4 text-amber-500"></i>
                    <h3 class="font-semibold text-slate-800 dark:text-white" x-text="mode === 'create' ? 'Tambah Jadwal' : 'Edit Jadwal'"></h3></div>
                <button @click="modal = false"><i data-lucide="x" class="w-5 h-5 text-slate-400"></i></button>
            </div>
            <form @submit.prevent="submitForm()" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="user" class="w-3.5 h-3.5 inline mr-1"></i>Guru</label>
                    <select x-model="form.guru_id" class="input-field" required>
                        <option value="">Pilih Guru</option>
                        @foreach($guru as $g)
                        <option value="{{ $g->id }}">{{ $g->nama }}</option>
                        @endforeach
                    </select>
                    <p x-show="errors.guru_id" x-text="errors.guru_id" class="text-xs text-red-500 mt-1"></p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="school" class="w-3.5 h-3.5 inline mr-1"></i>Kelas</label>
                        <select x-model="form.kelas_id" class="input-field" required>
                            <option value="">Pilih Kelas</option>
                            @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="book-marked" class="w-3.5 h-3.5 inline mr-1"></i>Mapel</label>
                        <select x-model="form.mapel_id" class="input-field" required>
                            <option value="">Pilih Mapel</option>
                            @foreach($mapel as $m)
                            <option value="{{ $m->id }}">{{ $m->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="calendar-range" class="w-3.5 h-3.5 inline mr-1"></i>Tahun Ajaran</label>
                    <select x-model="form.tahun_ajaran_id" class="input-field" required>
                        <option value="">Pilih Tahun Ajaran</option>
                        @foreach($tahunAjaran as $ta)
                        <option value="{{ $ta->id }}">{{ $ta->nama }} - {{ $ta->semester }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="calendar-days" class="w-3.5 h-3.5 inline mr-1"></i>Hari</label>
                        <select x-model="form.hari" class="input-field" required>
                            <option value="">Hari</option>
                            @foreach($namaHari as $num => $nama)
                            <option value="{{ $num }}">{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="clock-3" class="w-3.5 h-3.5 inline mr-1"></i>Mulai</label>
                        <input type="time" x-model="form.jam_mulai" class="input-field" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="clock-9" class="w-3.5 h-3.5 inline mr-1"></i>Selesai</label>
                        <input type="time" x-model="form.jam_selesai" class="input-field" required>
                    </div>
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
function jadwalManager() {
    const defaultForm = { guru_id: '', kelas_id: '', mapel_id: '', tahun_ajaran_id: '', hari: '', jam_mulai: '', jam_selesai: '' };
    return {
        modal: false, mode: 'create', loading: false, errorMsg: '', errors: {}, form: {...defaultForm}, editId: null,
        init() { this.$nextTick(() => lucide.createIcons()); },
        openCreate() { this.mode = 'create'; this.form = {...defaultForm}; this.errors = {}; this.errorMsg = ''; this.modal = true; this.$nextTick(() => lucide.createIcons()); },
        openEdit(item) {
            this.mode = 'edit'; this.editId = item.id;
            this.form = { guru_id: String(item.guru_id), kelas_id: String(item.kelas_id), mapel_id: String(item.mapel_id), tahun_ajaran_id: String(item.tahun_ajaran_id), hari: String(item.hari), jam_mulai: item.jam_mulai?.substring(0,5), jam_selesai: item.jam_selesai?.substring(0,5) };
            this.errors = {}; this.errorMsg = ''; this.modal = true; this.$nextTick(() => lucide.createIcons());
        },
        async submitForm() {
            this.loading = true; this.errors = {}; this.errorMsg = '';
            const url = this.mode === 'create' ? '{{ route('admin.jadwal.store') }}' : `/admin/jadwal/${this.editId}`;
            try {
                const res = await fetch(url, { method: this.mode === 'create' ? 'POST' : 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify(this.form) });
                const data = await res.json();
                if (!res.ok) { if (data.errors) this.errors = Object.fromEntries(Object.entries(data.errors).map(([k,v]) => [k,v[0]])); else this.errorMsg = data.message; }
                else { this.modal = false; Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' }); setTimeout(() => location.reload(), 1200); }
            } catch { this.errorMsg = 'Gagal terhubung.'; }
            finally { this.loading = false; }
        },
        async deleteJadwal(id) {
            const { isConfirmed } = await Swal.fire({ title: 'Hapus Jadwal?', text: 'Data jadwal ini akan dihapus.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Hapus', cancelButtonText: 'Batal', confirmButtonColor: '#ef4444' });
            if (!isConfirmed) return;
            const res = await fetch(`/admin/jadwal/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } });
            const data = await res.json();
            Swal.fire({ icon: 'success', title: 'Dihapus!', text: data.message, timer: 1200, showConfirmButton: false, toast: true, position: 'top-end' });
            setTimeout(() => location.reload(), 1000);
        }
    }
}
</script>
@endpush
