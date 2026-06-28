@extends('layouts.app')
@section('title', 'Tahun Ajaran')
@section('page-title', 'Tahun Ajaran')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Admin</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i><span class="text-slate-700 dark:text-zinc-200 font-medium">Tahun Ajaran</span>
@endsection
@section('content')
<div x-data="taManager()" x-init="init()">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">Tahun Ajaran</h2>
            <p class="text-sm text-slate-400 dark:text-zinc-500">Kelola periode akademik</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="openClone()" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/30 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 rounded-lg transition-colors">
                <i data-lucide="copy" class="w-4 h-4"></i> Clone Jadwal
            </button>
            <button @click="openCreate()" class="btn-primary">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah
            </button>
        </div>
    </div>

    <div class="card overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 dark:bg-zinc-800/60 border-b border-slate-200 dark:border-zinc-700/50">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider w-10">#</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Semester</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Jadwal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-zinc-700/50">
                @forelse($tahunAjaran as $ta)
                <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                    <td class="px-4 py-3.5 text-slate-400 dark:text-zinc-500 text-xs">{{ $loop->iteration + ($tahunAjaran->currentPage() - 1) * $tahunAjaran->perPage() }}</td>
                    <td class="px-4 py-3.5 font-semibold text-slate-700 dark:text-slate-200">{{ $ta->nama }}</td>
                    <td class="px-4 py-3.5">
                        <span class="badge {{ $ta->semester === 'Ganjil' ? 'bg-blue-100 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400' : 'bg-purple-100 dark:bg-purple-950/40 text-purple-700 dark:text-purple-400' }}">
                            {{ $ta->semester }}
                        </span>
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-2">
                            @if($ta->jadwal_count > 0)
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-600 dark:text-zinc-400">
                                    <i data-lucide="calendar-check" class="w-3.5 h-3.5 text-green-500"></i>
                                    {{ $ta->jadwal_count }} jadwal
                                </span>
                            @else
                                <span class="text-xs text-slate-400 dark:text-zinc-600">Belum ada</span>
                            @endif
                            
                            <button @click="generateJadwal({{ $ta->id }}, {{ $ta->jadwal_count }})" 
                                class="inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium bg-slate-100 hover:bg-indigo-100 text-slate-600 hover:text-indigo-700 dark:bg-zinc-800 dark:hover:bg-indigo-900/40 dark:text-zinc-300 dark:hover:text-indigo-400 transition-colors rounded-lg"
                                title="Generate Jadwal Otomatis">
                                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                                Generate
                            </button>
                        </div>
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
                            <button @click="cetakJadwal({{ $ta->id }}, {{ $ta->jadwal_count }})"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-zinc-700/50 rounded-lg transition-colors">
                                <i data-lucide="printer" class="w-3.5 h-3.5"></i> Cetak
                            </button>
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
                            <button @click="deleteTA({{ $ta->id }}, {{ $ta->jadwal_count }})" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-colors">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-12 text-center text-slate-400"><i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2 opacity-50"></i><p class="text-sm">Belum ada tahun ajaran</p></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah/Edit -->
    <div x-show="modal" x-transition.opacity
         x-effect="document.documentElement.style.overflow = modal ? 'hidden' : ''; document.body.style.overflow = modal ? 'hidden' : ''"
         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
         @click.self="modal = false">
        <div x-show="modal" x-transition.scale.95 @click.stop class="w-full max-w-sm bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden">
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

    <!-- Modal Clone Jadwal -->
    <div x-show="cloneModal" x-transition.opacity
         x-effect="document.documentElement.style.overflow = cloneModal ? 'hidden' : ''; document.body.style.overflow = cloneModal ? 'hidden' : ''"
         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
         @click.self="cloneModal = false">
        <div x-show="cloneModal" x-transition.scale.95 @click.stop class="w-full max-w-sm bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-zinc-700">
                <div class="flex items-center gap-2">
                    <i data-lucide="copy" class="w-4 h-4 text-indigo-500"></i>
                    <h3 class="font-semibold text-slate-800 dark:text-white">Clone Jadwal Pelajaran</h3>
                </div>
                <button @click="cloneModal = false"><i data-lucide="x" class="w-5 h-5 text-slate-400"></i></button>
            </div>
            <form @submit.prevent="submitClone()" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">Dari Tahun Ajaran (Sumber)</label>
                    <select x-model="cloneForm.source_id" @change="resetTargetIfSame()" class="input-field" required>
                        <option value="">Pilih sumber</option>
                        @foreach($tahunAjaranAll->where('jadwal_count', '>', 0) as $ta)
                        <option value="{{ $ta->id }}">{{ $ta->nama }} — {{ $ta->semester }}{{ $ta->is_aktif ? ' ★ Aktif' : '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center justify-center text-slate-400 dark:text-zinc-600">
                    <i data-lucide="arrow-down" class="w-5 h-5"></i>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">Ke Tahun Ajaran (Target)</label>
                    <select x-model="cloneForm.target_id" class="input-field" required>
                        <option value="">Pilih target</option>
                        <template x-for="ta in tahunAjaranAll.filter(t => String(t.id) !== String(cloneForm.source_id))" :key="ta.id">
                            <option :value="ta.id" x-text="ta.nama + ' — ' + ta.semester + (ta.is_aktif ? ' ★ Aktif' : '')"></option>
                        </template>
                    </select>
                </div>
                <div class="rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/40 px-4 py-3 flex gap-2">
                    <i data-lucide="triangle-alert" class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5"></i>
                    <p class="text-xs text-amber-700 dark:text-amber-400">Jadwal yang sudah ada di tahun ajaran target akan <strong>dihapus permanen</strong> dan diganti dengan jadwal dari sumber.</p>
                </div>
                <p x-show="cloneError" x-text="cloneError" class="text-xs text-red-500"></p>
                <div class="flex gap-3 pt-1">
                    <button type="button" @click="cloneModal = false" class="btn-secondary flex-1">Batal</button>
                    <button type="submit" :disabled="cloneLoading" class="btn-primary flex-1">
                        <i data-lucide="copy" class="w-4 h-4" x-show="!cloneLoading"></i>
                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin" x-show="cloneLoading"></i>
                        <span x-text="cloneLoading ? 'Menyalin...' : 'Clone Jadwal'"></span>
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
        cloneModal: false, cloneLoading: false, cloneError: '',
        cloneForm: { source_id: '', target_id: '' },
        tahunAjaranAll: @json($tahunAjaranAll),

        init() { this.$nextTick(() => lucide.createIcons()); },

        openCreate() {
            this.mode = 'create'; this.form = { nama: '', semester: '', is_aktif: false };
            this.errorMsg = ''; this.modal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        openEdit(item) {
            this.mode = 'edit'; this.editId = item.id;
            this.form = { nama: item.nama, semester: item.semester, is_aktif: !!item.is_aktif };
            this.errorMsg = ''; this.modal = true;
            this.$nextTick(() => {
                lucide.createIcons();
                this.$root.querySelectorAll('select').forEach(el => {
                    if (el.tomselect) el.tomselect.setValue(el.value);
                });
            });
        },

        openClone() {
            this.cloneForm = { source_id: '', target_id: '' };
            this.cloneError = ''; this.cloneModal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        resetTargetIfSame() {
            if (String(this.cloneForm.target_id) === String(this.cloneForm.source_id)) {
                this.cloneForm.target_id = '';
            }
        },

        cetakJadwal(id, jadwalCount) {
            if (jadwalCount === 0) {
                Swal.fire({
                    icon: 'info', title: 'Belum Ada Jadwal',
                    text: 'Belum ada jadwal untuk tahun ajaran ini.',
                    toast: true, position: 'top-end', timer: 2500, showConfirmButton: false
                });
                return;
            }
            window.open(`/admin/jadwal/print/semua?tahun_ajaran_id=${id}`, '_blank');
        },

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

        async deleteTA(id, jadwalCount) {
            if (jadwalCount > 0) {
                const { value } = await Swal.fire({
                    title: 'Hapus Tahun Ajaran?',
                    html: `<p style="font-size:0.875rem">Terdapat <strong>${jadwalCount} jadwal</strong> yang akan ikut terhapus.</p>
                           <p style="font-size:0.875rem;margin-top:6px">Ketik <strong>Yakin</strong> untuk konfirmasi.</p>`,
                    input: 'text',
                    inputPlaceholder: 'Ketik Yakin di sini',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#ef4444',
                    preConfirm: (val) => {
                        if (val !== 'Yakin') {
                            Swal.showValidationMessage('Harus mengetik "Yakin" (huruf kapital Y)');
                            return false;
                        }
                    }
                });
                if (!value) return;
            } else {
                const { isConfirmed } = await Swal.fire({ title: 'Hapus Tahun Ajaran?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Hapus', cancelButtonText: 'Batal', confirmButtonColor: '#ef4444' });
                if (!isConfirmed) return;
            }
            const res = await fetch(`/admin/tahun-ajaran/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } });
            const data = await res.json();
            if (res.ok) { Swal.fire({ icon: 'success', title: 'Dihapus!', text: data.message, timer: 1200, showConfirmButton: false, toast: true, position: 'top-end' }); setTimeout(() => location.reload(), 1000); }
            else { Swal.fire({ icon: 'error', title: 'Gagal', text: data.message }); }
        },

        async submitClone() {
            if (!this.cloneForm.source_id || !this.cloneForm.target_id) {
                this.cloneError = 'Pilih sumber dan target terlebih dahulu.'; return;
            }
            if (this.cloneForm.source_id === this.cloneForm.target_id) {
                this.cloneError = 'Sumber dan target tidak boleh sama.'; return;
            }

            const { isConfirmed } = await Swal.fire({
                title: 'Clone Jadwal?',
                text: 'Jadwal di tahun ajaran target akan dihapus dan diganti dengan jadwal dari sumber.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Clone',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#6366f1'
            });
            if (!isConfirmed) return;

            this.cloneLoading = true; this.cloneError = '';
            try {
                const res = await fetch(`/admin/tahun-ajaran/${this.cloneForm.target_id}/clone-jadwal`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ source_id: this.cloneForm.source_id })
                });
                const data = await res.json();
                if (!res.ok) { this.cloneError = data.message || 'Gagal menyalin jadwal.'; }
                else {
                    this.cloneModal = false;
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' });
                    setTimeout(() => location.reload(), 1800);
                }
            } catch { this.cloneError = 'Terjadi kesalahan. Coba lagi.'; }
            finally { this.cloneLoading = false; }
        },

        async generateJadwal(id, jadwalCount) {
            let htmlText = `<p style="font-size:0.875rem; text-align: left; margin-bottom: 0.5rem">Jadwal akan disusun otomatis berdasarkan data Tugas Mengajar dan Jam Pelajaran.</p>`;
            htmlText += `<div style="text-align: left;" class="mt-3">
                            <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">Metode Penyusunan</label>
                            <select id="generate-opsi" onchange="document.getElementById('generate-maxjam-wrap').style.display = this.value === 'max_4' ? 'block' : 'none'" class="w-full text-sm rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                                <option value="max_4">Opsi 1: Pisahkan maksimal N jam berturut-turut</option>
                                <option value="all_at_once">Opsi 2: Langsung semua jam sekaligus</option>
                            </select>
                            <div id="generate-maxjam-wrap" class="mt-3">
                                <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">Maksimal jam berturut-turut</label>
                                <input id="generate-maxjam" type="number" min="1" max="10" value="4" class="w-full text-sm rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
                                <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Jam mengajar dipecah agar tidak lebih dari nilai ini berturut-turut dalam satu hari (1–10).</p>
                            </div>
                         </div>`;

            if (jadwalCount > 0) {
                htmlText += `<div style="text-align: left;" class="mt-4 p-3 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800/50">
                                <p class="text-sm text-red-600 dark:text-red-400 font-semibold mb-1"><i data-lucide="alert-triangle" class="w-4 h-4 inline-block mr-1"></i>Peringatan!</p>
                                <p class="text-xs text-red-500 dark:text-red-300">Sudah ada ${jadwalCount} jadwal di tahun ajaran ini. Proses generate akan <strong>menghapus/menggantikan</strong> seluruh jadwal yang sudah ada sebelumnya.</p>
                             </div>`;
            }

            const { isConfirmed, value: result } = await Swal.fire({
                title: 'Generate Jadwal Otomatis',
                html: htmlText,
                icon: jadwalCount > 0 ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonText: 'Generate',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#6366f1',
                didOpen: () => lucide.createIcons(),
                preConfirm: () => {
                    const opsi = document.getElementById('generate-opsi').value;
                    const raw = parseInt(document.getElementById('generate-maxjam').value, 10);
                    if (opsi === 'max_4' && (isNaN(raw) || raw < 1 || raw > 10)) {
                        Swal.showValidationMessage('Maksimal jam harus antara 1 sampai 10');
                        return false;
                    }
                    return { opsi, maxJam: isNaN(raw) ? 4 : raw };
                }
            });

            if (!isConfirmed) return;
            const { opsi, maxJam } = result;

            Swal.fire({
                title: 'Menyusun Jadwal...',
                html: 'Mohon tunggu, proses ini mungkin memakan waktu beberapa detik.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const res = await fetch(`/admin/tahun-ajaran/${id}/generate-jadwal`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ opsi, max_jam: maxJam })
                });
                const data = await res.json();
                
                if (!res.ok) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message });
                } else {
                    Swal.fire({ 
                        icon: 'success', 
                        title: 'Selesai!', 
                        text: data.message,
                        timer: 2000, 
                        showConfirmButton: false, 
                        toast: true, 
                        position: 'top-end' 
                    });
                    setTimeout(() => location.reload(), 1800);
                }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan sistem.' });
            }
        }
    }
}
</script>
@endpush
