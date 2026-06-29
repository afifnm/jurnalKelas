@extends('layouts.app')
@section('title', 'Jam Pelajaran')
@section('page-title', 'Jam Pelajaran')
@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Admin</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i><span class="text-slate-700 dark:text-zinc-200 font-medium">Jam Pelajaran</span>
@endsection

@section('content')
<div x-data="jamPelajaranManager()">

<div class="flex items-center justify-between mb-5">
    <div>
        <h2 class="text-lg font-bold text-slate-800 dark:text-white">Jam Pelajaran</h2>
        <p class="text-sm text-slate-400 dark:text-zinc-500">Kelola slot jam pelajaran dan jam istirahat untuk setiap hari</p>
    </div>
    <a href="{{ route('admin.jadwal.index') }}" class="btn-secondary text-sm">
        <i data-lucide="calendar" class="w-4 h-4"></i> Ke Jadwal
    </a>
</div>

{{-- Tab hari --}}
<div class="flex gap-1 mb-5 overflow-x-auto pb-1">
    @foreach($namaHari as $num => $nama)
    <button @click="activeHari = {{ $num }}"
            :class="activeHari === {{ $num }}
                ? 'bg-amber-400 text-zinc-900 shadow-sm'
                : 'bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 text-slate-600 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-700'"
            class="flex-shrink-0 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
        {{ $nama }}
        @php $jmlSlot = isset($slots[$num]) ? $slots[$num]->count() : 0; @endphp
        @if($jmlSlot > 0)
        <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold"
              :class="activeHari === {{ $num }} ? 'bg-zinc-900/20 text-zinc-800' : 'bg-slate-100 dark:bg-zinc-700 text-slate-500'">
            {{ $jmlSlot }}
        </span>
        @endif
    </button>
    @endforeach
</div>

{{-- Konten per hari --}}
@foreach($namaHari as $hariNum => $hariNama)
<div x-show="activeHari === {{ $hariNum }}" style="display:none">
    <div class="card overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-zinc-700/50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4 text-amber-500"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 dark:text-white text-sm">{{ $hariNama }}</h3>
                    <p class="text-xs text-slate-400 dark:text-zinc-500"
                       id="slot-count-{{ $hariNum }}">{{ isset($slots[$hariNum]) ? $slots[$hariNum]->count() : 0 }} slot jam</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button @click="openClone({{ $hariNum }}, '{{ $hariNama }}')"
                        class="btn-secondary text-xs py-1.5 text-purple-600 dark:text-purple-400 border-purple-200 dark:border-purple-800 hover:bg-purple-50 dark:hover:bg-purple-950/30">
                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                    <span class="hidden sm:inline">Clone ke Hari Lain</span>
                </button>
                <button @click="openCreate({{ $hariNum }})"
                        class="btn-primary text-xs py-1.5">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span class="hidden sm:inline">Tambah Jam</span>
                </button>
            </div>
        </div>

        @php $slotsHari = $slots[$hariNum] ?? collect(); @endphp
        @if($slotsHari->isEmpty())
        <div id="empty-{{ $hariNum }}" class="flex flex-col items-center justify-center py-10 text-slate-400 dark:text-zinc-600">
            <i data-lucide="clock-x" class="w-8 h-8 mb-2 opacity-40"></i>
            <p class="text-sm mb-3">Belum ada slot jam untuk hari ini</p>
            <button @click="openCreate({{ $hariNum }})" class="btn-primary text-xs py-1.5">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah Jam Pertama
            </button>
        </div>
        @endif

        <div id="slot-list-{{ $hariNum }}" class="divide-y divide-slate-100 dark:divide-zinc-700/50">
            @foreach($slotsHari->sortBy('jam_mulai') as $slot)
            <div data-slot-id="{{ $slot->id }}" data-jam-mulai="{{ substr($slot->jam_mulai, 0, 5) }}" class="flex items-center gap-4 px-5 py-3 {{ $slot->is_istirahat ? 'bg-sky-50/50 dark:bg-sky-950/10' : '' }}">
                @if($slot->is_istirahat)
                <div class="icon-wrapper w-8 h-8 rounded-lg bg-sky-100 dark:bg-sky-950/50 flex items-center justify-center flex-shrink-0">
                    <i data-lucide="coffee" class="w-4 h-4 text-sky-500 dark:text-sky-400"></i>
                </div>
                @else
                <div class="icon-wrapper w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-bold text-amber-700 dark:text-amber-400">{{ $slot->jam_ke }}</span>
                </div>
                @endif
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        @if($slot->is_istirahat)
                        <p class="text-sm font-semibold text-sky-700 dark:text-sky-300">Istirahat</p>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400">ISTIRAHAT</span>
                        @else
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Jam ke-{{ $slot->jam_ke }}</p>
                        @endif
                    </div>
                    <p class="text-xs text-slate-400 dark:text-zinc-500 font-mono">
                        {{ substr($slot->jam_mulai, 0, 5) }} – {{ substr($slot->jam_selesai, 0, 5) }}
                    </p>
                </div>
                <div class="flex items-center gap-1 flex-shrink-0">
                    <button onclick="_jamPelajaranEdit({{ $slot->toJson() }})"
                            class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30 rounded-lg transition-colors">
                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                    </button>
                    <button onclick="_jamPelajaranDelete({{ $slot->id }}, {{ $slot->jam_ke }}, {{ $slot->hari }})"
                            class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-colors">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endforeach

{{-- Modal Tambah/Edit Slot --}}
<div x-show="modal" x-transition.opacity
     x-effect="document.documentElement.style.overflow = modal ? 'hidden' : ''"
     class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
     @click.self="modal = false" style="display:none">
    <div x-show="modal" x-transition.scale.95 @click.stop
         class="w-full max-w-sm bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-zinc-700">
            <div class="flex items-center gap-2">
                <i data-lucide="clock" class="w-4 h-4 text-amber-500"></i>
                <h3 class="font-semibold text-slate-800 dark:text-white"
                    x-text="mode === 'create' ? 'Tambah Slot Jam' : 'Edit Slot Jam'"></h3>
            </div>
            <button @click="modal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-zinc-300">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form @submit.prevent="submitForm()" class="p-6 space-y-4">
            {{-- Checkbox Istirahat --}}
            <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-zinc-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-zinc-800/60 transition-colors"
                   :class="form.is_istirahat ? 'border-sky-300 dark:border-sky-700 bg-sky-50/60 dark:bg-sky-950/20' : ''">
                <input type="checkbox" x-model="form.is_istirahat"
                       @change="autoFillIstirahat"
                       class="w-4 h-4 rounded border-slate-300 text-sky-500 focus:ring-sky-400">
                <div>
                    <p class="text-sm font-medium text-slate-700 dark:text-zinc-200">Jam Istirahat</p>
                    <p class="text-xs text-slate-400 dark:text-zinc-500">Centang jika ini adalah slot istirahat (tidak bisa diisi jadwal)</p>
                </div>
                <i data-lucide="coffee" class="w-4 h-4 text-sky-400 ml-auto flex-shrink-0" x-show="form.is_istirahat" style="display:none"></i>
            </label>

            <div class="grid gap-3" :class="form.is_istirahat ? 'grid-cols-2' : 'grid-cols-3'">
                <div x-show="!form.is_istirahat" style="display:block">
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">Jam Ke</label>
                    <select x-model="form.jam_ke"
                            @change="autoFillJam"
                            class="w-full text-sm border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 bg-white dark:bg-zinc-800 text-slate-700 dark:text-zinc-200 focus:outline-none focus:border-amber-400 dark:focus:border-amber-500"
                            :required="!form.is_istirahat">
                        <option value="">–</option>
                        @foreach(range(0, 12) as $ke)
                        <option value="{{ $ke }}">{{ $ke }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">Mulai</label>
                    <input type="time" x-model="form.jam_mulai"
                           class="w-full text-sm border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 bg-white dark:bg-zinc-800 text-slate-700 dark:text-zinc-200 focus:outline-none focus:border-amber-400 dark:focus:border-amber-500"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">Selesai</label>
                    <input type="time" x-model="form.jam_selesai"
                           class="w-full text-sm border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 bg-white dark:bg-zinc-800 text-slate-700 dark:text-zinc-200 focus:outline-none focus:border-amber-400 dark:focus:border-amber-500"
                           required>
                </div>
            </div>
            <div x-show="errorMsg" class="flex items-start gap-2 p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800/40 rounded-xl" style="display:none">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5"></i>
                <p x-text="errorMsg" class="text-xs text-red-600 dark:text-red-400"></p>
            </div>
            <div class="flex gap-3">
                <button type="button" @click="modal = false" class="btn-secondary flex-1">Batal</button>
                <button type="submit" :disabled="loading" class="btn-primary flex-1 flex items-center justify-center gap-1.5">
                    <span x-text="loading ? 'Menyimpan...' : (mode === 'create' ? 'Tambah' : 'Simpan')"></span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Clone Hari --}}
<div x-show="cloneModal" x-transition.opacity
     x-effect="document.documentElement.style.overflow = cloneModal ? 'hidden' : ''"
     class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
     @click.self="cloneModal = false" style="display:none">
    <div x-show="cloneModal" x-transition.scale.95 @click.stop
         class="w-full max-w-sm bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-zinc-700">
            <div class="flex items-center gap-2">
                <i data-lucide="copy" class="w-4 h-4 text-purple-500"></i>
                <h3 class="font-semibold text-slate-800 dark:text-white">Clone Jam Pelajaran</h3>
            </div>
            <button @click="cloneModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-zinc-300">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/40 rounded-xl">
                <p class="text-xs text-amber-700 dark:text-amber-400">
                    <span class="font-semibold">Hari asal:</span>
                    <span x-text="cloneHariNama"></span>
                </p>
                <p class="text-xs text-amber-600 dark:text-amber-500 mt-0.5">Slot yang sudah ada di hari tujuan akan ditimpa.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-2">Clone ke hari:</label>
                <div class="space-y-2">
                    @foreach($namaHari as $num => $nama)
                    <label class="flex items-center gap-2 cursor-pointer"
                           :class="{{ $num }} === cloneHariAsal ? 'opacity-40 pointer-events-none' : ''">
                        <input type="checkbox" :value="{{ $num }}" x-model="cloneTujuan"
                               class="w-4 h-4 rounded border-slate-300 text-amber-500 focus:ring-amber-400">
                        <span class="text-sm text-slate-700 dark:text-zinc-300">{{ $nama }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div x-show="cloneErrorMsg" class="flex items-start gap-2 p-3 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800/40 rounded-xl" style="display:none">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5"></i>
                <p x-text="cloneErrorMsg" class="text-xs text-red-600 dark:text-red-400"></p>
            </div>
            <div class="flex gap-3">
                <button type="button" @click="cloneModal = false" class="btn-secondary flex-1">Batal</button>
                <button @click="submitClone()" :disabled="cloneLoading || cloneTujuan.length === 0"
                        class="btn-primary flex-1 flex items-center justify-center gap-1.5 bg-purple-600 hover:bg-purple-700 disabled:opacity-40">
                    <i data-lucide="copy" class="w-4 h-4"></i>
                    <span x-text="cloneLoading ? 'Meng-clone...' : `Clone ke ${cloneTujuan.length} hari`"></span>
                </button>
            </div>
        </div>
    </div>
</div>

</div>{{-- end x-data --}}
@endsection

@push('scripts')
<script>
function jamPelajaranManager() {
    const defaultForm = { jam_ke: '', jam_mulai: '', jam_selesai: '', hari: '', is_istirahat: false };

    // Jumlah slot istirahat yang sudah ada per hari (diisi dari PHP, diupdate dinamis)
    const istirahatCount = {
        @foreach($namaHari as $hariNum => $_)
        {{ $hariNum }}: {{ isset($slots[$hariNum]) ? $slots[$hariNum]->where('is_istirahat', true)->count() : 0 }},
        @endforeach
    };
    return {
        activeHari: {{ array_key_first($namaHari) ?? 1 }},
        modal: false, mode: 'create', loading: false, errorMsg: '', editId: null,
        form: { ...defaultForm },

        cloneModal: false, cloneLoading: false, cloneErrorMsg: '',
        cloneHariAsal: null, cloneHariNama: '', cloneTujuan: [],

        init() {
            window._jamPelajaranEdit   = (item) => this.openEdit(item);
            window._jamPelajaranDelete = (id, jamKe, hari) => this.deleteSlot(id, jamKe, hari);
        },

        openCreate(hari) {
            this.mode = 'create';
            this.editId = null;
            this.form = { ...defaultForm, hari: String(hari) };
            this.errorMsg = '';
            this.modal = true;
        },

        openEdit(item) {
            this.mode = 'edit';
            this.editId = item.id;
            this.form = {
                hari:         String(item.hari),
                jam_ke:       String(item.jam_ke),
                jam_mulai:    (item.jam_mulai || '').substring(0, 5),
                jam_selesai:  (item.jam_selesai || '').substring(0, 5),
                is_istirahat: !!item.is_istirahat,
            };
            this.errorMsg = '';
            this.modal = true;
        },

        autoFillIstirahat() {
            if (!this.form.is_istirahat) return;
            // Hanya auto-fill saat mode create dan field masih kosong
            if (this.mode === 'edit') return;

            const hari = parseInt(this.form.hari);
            const jumlah = istirahatCount[hari] ?? 0;

            if (jumlah === 0) {
                this.form.jam_mulai  = '10:00';
                this.form.jam_selesai = '10:15';
            } else if (jumlah === 1) {
                this.form.jam_mulai  = '11:45';
                this.form.jam_selesai = '12:15';
            }
            // jika sudah ≥2 istirahat, biarkan user isi manual
        },

        autoFillJam() {
            const defaultTimes = {
                0: { mulai: '06:15', selesai: '07:00' },
                1: { mulai: '07:00', selesai: '07:45' },
                2: { mulai: '07:45', selesai: '08:30' },
                3: { mulai: '08:30', selesai: '09:15' },
                4: { mulai: '09:15', selesai: '10:00' },
                5: { mulai: '10:15', selesai: '11:00' },
                6: { mulai: '11:00', selesai: '11:45' },
                7: { mulai: '11:45', selesai: '12:30' },
                8: { mulai: '13:00', selesai: '13:45' },
                9: { mulai: '13:45', selesai: '14:30' },
                10: { mulai: '14:30', selesai: '15:15' },
                11: { mulai: '15:15', selesai: '16:00' },
                12: { mulai: '16:00', selesai: '16:45' },
            };
            const selected = parseInt(this.form.jam_ke);
            if (!isNaN(selected) && defaultTimes[selected]) {
                this.form.jam_mulai = defaultTimes[selected].mulai;
                this.form.jam_selesai = defaultTimes[selected].selesai;
            }
        },

        async submitForm() {
            this.loading = true;
            this.errorMsg = '';
            const url    = this.mode === 'create' ? '{{ route('admin.jam-pelajaran.store') }}' : `/admin/jam-pelajaran/${this.editId}`;
            const method = this.mode === 'create' ? 'POST' : 'PUT';
            try {
                const res  = await fetch(url, {
                    method,
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify(this.form),
                });
                const data = await res.json();
                if (!res.ok) {
                    const msgs = data.errors ? Object.values(data.errors).flat() : [data.message || 'Terjadi kesalahan.'];
                    this.errorMsg = msgs.join(' ');
                    return;
                }
                this.modal = false;
                this.upsertSlotDom(data.slot, this.mode === 'create');
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' });
            } catch {
                this.errorMsg = 'Gagal terhubung ke server.';
            } finally {
                this.loading = false;
            }
        },

        async deleteSlot(id, jamKe, hari) {
            const { isConfirmed } = await Swal.fire({
                title: 'Hapus Slot?', text: `Jam ke-${jamKe} akan dihapus.`, icon: 'warning',
                showCancelButton: true, confirmButtonText: 'Hapus', cancelButtonText: 'Batal', confirmButtonColor: '#ef4444',
            });
            if (!isConfirmed) return;
            const res  = await fetch(`/admin/jam-pelajaran/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            });
            const data = await res.json();
            const card = document.querySelector(`[data-slot-id="${id}"]`);
            if (card) card.remove();
            this.updateSlotCount(hari, -1);
            Swal.fire({ icon: 'success', title: 'Dihapus!', text: data.message, timer: 1200, showConfirmButton: false, toast: true, position: 'top-end' });
        },

        openClone(hari, nama) {
            this.cloneHariAsal  = hari;
            this.cloneHariNama  = nama;
            this.cloneTujuan    = [];
            this.cloneErrorMsg  = '';
            this.cloneModal     = true;
        },

        async submitClone() {
            if (!this.cloneTujuan.length) return;
            this.cloneLoading  = true;
            this.cloneErrorMsg = '';
            try {
                const res  = await fetch('{{ route('admin.jam-pelajaran.clone') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ hari_asal: this.cloneHariAsal, hari_tujuan: this.cloneTujuan }),
                });
                const data = await res.json();
                if (!res.ok) { this.cloneErrorMsg = data.message || 'Gagal clone.'; return; }
                this.cloneModal = false;
                Swal.fire({ icon: 'success', title: 'Clone Berhasil!', text: data.message, timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' });
                setTimeout(() => location.reload(), 1800);
            } catch {
                this.cloneErrorMsg = 'Gagal terhubung ke server.';
            } finally {
                this.cloneLoading = false;
            }
        },

        upsertSlotDom(slot, isNew) {
            const list    = document.getElementById(`slot-list-${slot.hari}`);
            const empty   = document.getElementById(`empty-${slot.hari}`);
            const mulai   = (slot.jam_mulai   || '').substring(0, 5);
            const selesai = (slot.jam_selesai || '').substring(0, 5);
            const isIstirahat = !!slot.is_istirahat;

            if (!isNew) {
                // Update existing row
                const existing = document.querySelector(`[data-slot-id="${slot.id}"]`);
                if (existing) {
                    existing.className = `flex items-center gap-4 px-5 py-3${isIstirahat ? ' bg-sky-50/50 dark:bg-sky-950/10' : ''}`;
                    if (isIstirahat) {
                        existing.querySelector('.icon-wrapper').className = 'icon-wrapper w-8 h-8 rounded-lg bg-sky-100 dark:bg-sky-950/50 flex items-center justify-center flex-shrink-0';
                        existing.querySelector('.icon-wrapper').innerHTML = '<i data-lucide="coffee" class="w-4 h-4 text-sky-500 dark:text-sky-400"></i>';
                        existing.querySelector('.text-sm.font-semibold').textContent = 'Istirahat';
                        existing.querySelector('.text-sm.font-semibold').className = 'text-sm font-semibold text-sky-700 dark:text-sky-300';
                    } else {
                        existing.querySelector('.icon-wrapper').className = 'icon-wrapper w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center flex-shrink-0';
                        existing.querySelector('.icon-wrapper').innerHTML = `<span class="text-xs font-bold text-amber-700 dark:text-amber-400">${slot.jam_ke}</span>`;
                        existing.querySelector('.text-sm.font-semibold').textContent = `Jam ke-${slot.jam_ke}`;
                        existing.querySelector('.text-sm.font-semibold').className = 'text-sm font-semibold text-slate-700 dark:text-slate-200';
                    }
                    existing.querySelector('.font-mono').textContent = `${mulai} – ${selesai}`;
                    // Re-bind onclick buttons
                    const btns = existing.querySelectorAll('button');
                    btns[0].setAttribute('onclick', `_jamPelajaranEdit(${JSON.stringify(slot).replace(/"/g, '&quot;')})`);
                    btns[1].setAttribute('onclick', `_jamPelajaranDelete(${slot.id}, ${slot.jam_ke}, ${slot.hari})`);
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                    return;
                }
            }

            // Build new row HTML
            const iconHtml = isIstirahat
                ? `<div class="icon-wrapper w-8 h-8 rounded-lg bg-sky-100 dark:bg-sky-950/50 flex items-center justify-center flex-shrink-0"><i data-lucide="coffee" class="w-4 h-4 text-sky-500 dark:text-sky-400"></i></div>`
                : `<div class="icon-wrapper w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center flex-shrink-0"><span class="text-xs font-bold text-amber-700 dark:text-amber-400">${slot.jam_ke}</span></div>`;

            const labelHtml = isIstirahat
                ? `<div class="flex items-center gap-2"><p class="text-sm font-semibold text-sky-700 dark:text-sky-300">Istirahat</p><span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 dark:bg-sky-900/50 text-sky-600 dark:text-sky-400">ISTIRAHAT</span></div>`
                : `<div class="flex items-center gap-2"><p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Jam ke-${slot.jam_ke}</p></div>`;

            const html = `
                <div data-slot-id="${slot.id}" data-jam-mulai="${mulai}" class="flex items-center gap-4 px-5 py-3${isIstirahat ? ' bg-sky-50/50 dark:bg-sky-950/10' : ''}">
                    ${iconHtml}
                    <div class="flex-1 min-w-0">
                        ${labelHtml}
                        <p class="text-xs text-slate-400 dark:text-zinc-500 font-mono">${mulai} – ${selesai}</p>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button onclick="_jamPelajaranEdit(${JSON.stringify(slot).replace(/"/g, '&quot;')})"
                                class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30 rounded-lg transition-colors">
                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                        </button>
                        <button onclick="_jamPelajaranDelete(${slot.id}, ${slot.jam_ke}, ${slot.hari})"
                                class="inline-flex items-center px-2 py-1.5 text-xs font-medium text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-colors">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>`;

            const template = document.createElement('template');
            template.innerHTML = html.trim();
            const el = template.content.firstChild;

            // Insert sorted by jam_mulai
            const rows = [...list.querySelectorAll('[data-slot-id]')];
            let inserted = false;
            for (const row of rows) {
                const existingMulai = row.dataset.jamMulai || '99:99';
                if (existingMulai > mulai) { list.insertBefore(el, row); inserted = true; break; }
            }
            if (!inserted) list.appendChild(el);

            if (empty) empty.style.display = 'none';
            if (typeof lucide !== 'undefined') lucide.createIcons();
            this.updateSlotCount(slot.hari, 1);
            if (slot.is_istirahat) {
                istirahatCount[slot.hari] = (istirahatCount[slot.hari] ?? 0) + 1;
            }
        },

        updateSlotCount(hari, delta) {
            const el = document.getElementById(`slot-count-${hari}`);
            if (!el) return;
            const match = el.textContent.match(/(\d+)/);
            if (match) el.textContent = `${parseInt(match[1]) + delta} slot jam`;
        },
    };
}
</script>
@endpush
