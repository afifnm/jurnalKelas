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
        <p class="text-sm text-slate-400 dark:text-zinc-500">Kelola slot jam ke-1 s/d ke-8 untuk setiap hari</p>
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
            @foreach($slotsHari->sortBy('jam_ke') as $slot)
            <div data-slot-id="{{ $slot->id }}" class="flex items-center gap-4 px-5 py-3">
                <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-bold text-amber-700 dark:text-amber-400">{{ $slot->jam_ke }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Jam ke-{{ $slot->jam_ke }}</p>
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
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">Jam Ke</label>
                    <select x-model="form.jam_ke"
                            @change="autoFillJam"
                            class="w-full text-sm border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 bg-white dark:bg-zinc-800 text-slate-700 dark:text-zinc-200 focus:outline-none focus:border-amber-400 dark:focus:border-amber-500"
                            required>
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
    const defaultForm = { jam_ke: '', jam_mulai: '', jam_selesai: '', hari: '' };
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
                hari:        String(item.hari),
                jam_ke:      String(item.jam_ke),
                jam_mulai:   (item.jam_mulai || '').substring(0, 5),
                jam_selesai: (item.jam_selesai || '').substring(0, 5),
            };
            this.errorMsg = '';
            this.modal = true;
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

            if (!isNew) {
                // Update existing row
                const existing = document.querySelector(`[data-slot-id="${slot.id}"]`);
                if (existing) {
                    existing.querySelector('.text-sm.font-semibold').textContent = `Jam ke-${slot.jam_ke}`;
                    existing.querySelector('.font-mono').textContent = `${mulai} – ${selesai}`;
                    existing.querySelector('.text-xs.font-bold').textContent = slot.jam_ke;
                    // Re-bind onclick buttons
                    const btns = existing.querySelectorAll('button');
                    btns[0].setAttribute('onclick', `_jamPelajaranEdit(${JSON.stringify(slot).replace(/"/g, '&quot;')})`);
                    btns[1].setAttribute('onclick', `_jamPelajaranDelete(${slot.id}, ${slot.jam_ke}, ${slot.hari})`);
                    return;
                }
            }

            // Build new row HTML
            const html = `
                <div data-slot-id="${slot.id}" class="flex items-center gap-4 px-5 py-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center flex-shrink-0">
                        <span class="text-xs font-bold text-amber-700 dark:text-amber-400">${slot.jam_ke}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Jam ke-${slot.jam_ke}</p>
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

            // Insert sorted by jam_ke
            const rows = [...list.querySelectorAll('[data-slot-id]')];
            let inserted = false;
            for (const row of rows) {
                const existingKe = parseInt(row.querySelector('.text-xs.font-bold')?.textContent || '99');
                if (existingKe > slot.jam_ke) { list.insertBefore(el, row); inserted = true; break; }
            }
            if (!inserted) list.appendChild(el);

            if (empty) empty.style.display = 'none';
            if (typeof lucide !== 'undefined') lucide.createIcons();
            this.updateSlotCount(slot.hari, 1);
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
