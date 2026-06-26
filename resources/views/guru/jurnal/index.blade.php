@extends('layouts.app')
@section('title', 'Jurnal Mengajar')
@section('page-title', 'Jurnal Mengajar')

@section('content')
<div x-data="jurnalManager()" x-init="init()">

    <!-- Header -->
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">Jurnal Mengajar</h2>
            <p class="text-sm text-slate-400 dark:text-zinc-500">Riwayat dan pengisian jurnal harian</p>
        </div>
        <button @click="openCreate()" class="btn-primary">
            <i data-lucide="notebook-pen" class="w-4 h-4"></i> Isi Jurnal
        </button>
    </div>

    <!-- Jadwal Hari Ini (shortcut) -->
    @if($jadwalHariIni->isNotEmpty())
    <div class="card p-4 mb-4">
        <p class="text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider mb-3 flex items-center gap-2">
            <i data-lucide="calendar-clock" class="w-3.5 h-3.5 text-amber-500"></i>
            Jadwal Hari Ini ({{ now()->translatedFormat('l, d M Y') }})
        </p>
        <div class="flex flex-wrap gap-2">
            @foreach($jadwalHariIni as $j)
            @php $sudah = in_array($j->id, $sudahDiisiHariIni); @endphp
            <button @click="{{ $sudah ? '' : "openCreateWithJadwal({$j->id}, {$j->kelas_id}, {$j->mapel_id}, '{$j->jam_mulai}', '{$j->jam_selesai}')" }}"
                class="flex items-center gap-2 px-3 py-2 rounded-xl border text-sm transition-all {{ $sudah ? 'border-green-200 dark:border-green-800/40 bg-green-50 dark:bg-green-950/30 text-green-600 dark:text-green-400 cursor-default' : 'border-amber-200 dark:border-amber-800/40 bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-950/50 cursor-pointer' }}">
                <i :data-lucide="'{{ $sudah ? 'check-circle-2' : 'plus-circle' }}'" class="w-4 h-4"></i>
                <span class="font-medium">{{ $j->mapel->nama }}</span>
                <span class="text-xs opacity-70">{{ $j->kelas->nama }} {{ substr($j->jam_mulai,0,5) }}</span>
            </button>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Filter -->
    <div class="card p-4 mb-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="month" name="bulan" value="{{ request('bulan') }}" class="input-field w-auto text-sm">
            <select name="status" class="input-field w-auto text-sm">
                <option value="">Semua Status</option>
                <option value="draft" @selected(request('status') === 'draft')>Draft</option>
            </select>
            <select name="kelas_id" class="input-field w-auto text-sm">
                <option value="">Semua Kelas</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id }}" @selected(request('kelas_id') == $k->id)>{{ $k->nama }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary text-sm"><i data-lucide="filter" class="w-4 h-4"></i> Filter</button>
            @if(request()->hasAny(['bulan','status','kelas_id']))
            <a href="{{ route('guru.jurnal.index') }}" class="btn-secondary text-sm"><i data-lucide="x" class="w-4 h-4"></i> Reset</a>
            @endif
        </form>
    </div>

    <!-- Tabel Jurnal -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-zinc-800/60 border-b border-slate-200 dark:border-zinc-700/50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Kelas / Mapel</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Masuk</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Materi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-700/50">
                    @forelse($jurnal as $j)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="px-4 py-3.5">
                            <p class="font-semibold text-slate-700 dark:text-slate-200">{{ $j->tanggal->translatedFormat('l, j F Y') }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="font-medium text-slate-700 dark:text-slate-200">{{ $j->kelas->nama }}</p>
                            <p class="text-xs text-slate-400">{{ $j->mapel->nama }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="font-mono text-sm text-slate-600 dark:text-zinc-400">{{ $j->jam_masuk_aktual ? substr($j->jam_masuk_aktual, 0, 5) : '-' }}</p>
                            @if($j->is_terlambat)
                            <span class="text-[10px] text-red-500 dark:text-red-400 font-medium">+{{ $j->menit_terlambat }} menit</span>
                            @else
                            <span class="text-[10px] text-green-500 dark:text-green-400 font-medium">Tepat waktu</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="text-slate-600 dark:text-zinc-400 line-clamp-2 text-sm max-w-48">{{ $j->materi }}</p>
                            @if($j->lampiran->count())
                            <span class="text-[10px] text-slate-400 flex items-center gap-1 mt-0.5">
                                <i data-lucide="paperclip" class="w-3 h-3"></i>{{ $j->lampiran->count() }} lampiran
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="badge {{ $j->badge_color }}">{{ $j->status_label }}</span>
                            @if($j->status === 'revisi' && $j->catatan_validasi)
                            <p class="text-xs text-orange-600 dark:text-orange-400 mt-1 max-w-32 line-clamp-1" title="{{ $j->catatan_validasi }}">
                                {{ $j->catatan_validasi }}
                            </p>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                <button @click="viewDetail({{ $j->id }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/30 rounded-lg transition-colors">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail
                                </button>
                                @if($j->isEditableByGuru())
                                <button @click="openEdit({{ $j->id }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30 rounded-lg transition-colors">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit
                                </button>
                                @if($j->status === 'draft')
                                <button @click="deleteJurnal({{ $j->id }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg transition-colors">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                </button>
                                @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center">
                        <div class="flex flex-col items-center text-slate-400 dark:text-zinc-600">
                            <i data-lucide="notebook" class="w-10 h-10 mb-2 opacity-50"></i>
                            <p class="text-sm">Belum ada jurnal</p>
                            <button @click="openCreate()" class="mt-3 btn-primary text-xs">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Isi Jurnal Pertama
                            </button>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($jurnal->hasPages())
        <div class="px-4 py-3 border-t border-slate-100 dark:border-zinc-700/50">{{ $jurnal->links() }}</div>
        @endif
    </div>

    <!-- Modal Create/Edit Jurnal -->
    <div x-show="modal" x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         @click.self="modal = false">
        <div x-show="modal" x-transition.scale.95
             class="w-full max-w-2xl bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-zinc-700 flex-shrink-0">
                <div class="flex items-center gap-2">
                    <i data-lucide="notebook-pen" class="w-4 h-4 text-amber-500"></i>
                    <h3 class="font-semibold text-slate-800 dark:text-white" x-text="mode === 'create' ? 'Isi Jurnal Baru' : 'Edit Jurnal'"></h3>
                </div>
                <button @click="modal = false"><i data-lucide="x" class="w-5 h-5 text-slate-400"></i></button>
            </div>

            <form @submit.prevent="submitForm()" class="flex-1 overflow-y-auto p-6 space-y-4" enctype="multipart/form-data">
                <!-- Jadwal Picker -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                        <i data-lucide="calendar-clock" class="w-3.5 h-3.5 inline mr-1"></i>Pilih Jadwal (opsional)
                    </label>
                    <select x-model="form.jadwal_id" @change="onJadwalChange()" class="input-field">
                        <option value="">-- Pilih dari jadwal hari ini --</option>
                        @foreach($jadwalHariIni as $j)
                        <option value="{{ $j->id }}" data-kelas="{{ $j->kelas_id }}" data-mapel="{{ $j->mapel_id }}" data-mulai="{{ $j->jam_mulai }}">
                            {{ $j->mapel->nama }} — {{ $j->kelas->nama }} ({{ substr($j->jam_mulai,0,5) }})
                        </option>
                        @endforeach
                    </select>
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
                        <p x-show="errors.kelas_id" x-text="errors.kelas_id" class="text-xs text-red-500 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="book-marked" class="w-3.5 h-3.5 inline mr-1"></i>Mata Pelajaran</label>
                        <select x-model="form.mapel_id" class="input-field" required>
                            <option value="">Pilih Mapel</option>
                            @foreach($mapel as $m)
                            <option value="{{ $m->id }}">{{ $m->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="calendar" class="w-3.5 h-3.5 inline mr-1"></i>Tanggal</label>
                    <input type="date" x-model="form.tanggal" class="input-field" :max="today" required>
                    <p x-show="errors.tanggal" x-text="errors.tanggal" class="text-xs text-red-500 mt-1"></p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="clock-3" class="w-3.5 h-3.5 inline mr-1"></i>Jam Masuk Aktual</label>
                        <input type="time" x-model="form.jam_masuk_aktual" class="input-field" required>
                        <p x-show="errors.jam_masuk_aktual" x-text="errors.jam_masuk_aktual" class="text-xs text-red-500 mt-1"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="clock-9" class="w-3.5 h-3.5 inline mr-1"></i>Jam Keluar Aktual</label>
                        <input type="time" x-model="form.jam_keluar_aktual" class="input-field">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="book-open" class="w-3.5 h-3.5 inline mr-1"></i>Materi Pembelajaran</label>
                    <textarea x-model="form.materi" rows="3" class="input-field resize-none" placeholder="Tuliskan materi yang diajarkan..." required></textarea>
                    <p x-show="errors.materi" x-text="errors.materi" class="text-xs text-red-500 mt-1"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="layers" class="w-3.5 h-3.5 inline mr-1"></i>Metode Pembelajaran</label>
                    <input type="text" x-model="form.metode_pembelajaran" class="input-field" placeholder="Ceramah, diskusi, praktik, dll.">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="alert-triangle" class="w-3.5 h-3.5 inline mr-1"></i>Kendala</label>
                        <textarea x-model="form.kendala" rows="2" class="input-field resize-none" placeholder="Kendala selama KBM..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="lightbulb" class="w-3.5 h-3.5 inline mr-1"></i>Tindak Lanjut</label>
                        <textarea x-model="form.tindak_lanjut" rows="2" class="input-field resize-none" placeholder="Rencana tindak lanjut..."></textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="message-square" class="w-3.5 h-3.5 inline mr-1"></i>Catatan Tambahan</label>
                    <textarea x-model="form.catatan" rows="2" class="input-field resize-none" placeholder="Catatan lain jika ada..."></textarea>
                </div>

                <!-- Upload Lampiran -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-2">
                        <i data-lucide="paperclip" class="w-3.5 h-3.5 inline mr-1"></i>Lampiran Foto KBM
                    </label>
                    <div class="border-2 border-dashed border-slate-200 dark:border-zinc-700 rounded-xl p-4 text-center"
                         @dragover.prevent @drop.prevent="onDrop($event)"
                         :class="dragOver ? 'border-amber-400 bg-amber-50 dark:bg-amber-950/20' : ''"
                         @dragenter="dragOver = true" @dragleave="dragOver = false">
                        <input type="file" id="lampiran_upload" name="lampiran[]" multiple accept="image/*"
                               class="hidden" @change="onFilePick($event)">
                        <label for="lampiran_upload" class="cursor-pointer">
                            <i data-lucide="image-plus" class="w-8 h-8 text-slate-300 dark:text-zinc-600 mx-auto mb-2"></i>
                            <p class="text-sm text-slate-400 dark:text-zinc-500">Drop foto di sini atau <span class="text-amber-600 dark:text-amber-400 font-medium">klik untuk pilih</span></p>
                            <p class="text-xs text-slate-300 dark:text-zinc-600 mt-1">JPG/PNG/WEBP, maks 5MB per file, maks 5 foto</p>
                        </label>
                    </div>
                    <!-- Preview -->
                    <div x-show="previews.length > 0" class="flex gap-2 mt-3 flex-wrap">
                        <template x-for="(p, i) in previews" :key="i">
                            <div class="relative">
                                <img :src="p" class="w-16 h-16 object-cover rounded-lg border border-slate-200 dark:border-zinc-700">
                                <button type="button" @click="removeFile(i)"
                                    class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600">
                                    <i data-lucide="x" class="w-3 h-3"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <p x-show="errorMsg" x-text="errorMsg" class="text-sm text-red-500 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/40 p-3 rounded-xl"></p>

                <div class="flex gap-3 pt-2 border-t border-slate-100 dark:border-zinc-700/50">
                    <button type="button" @click="modal = false" class="btn-secondary flex-1">Batal</button>
                    <button type="submit" :disabled="loading" class="btn-primary flex-1">
                        <i data-lucide="save" class="w-4 h-4" x-show="!loading"></i>
                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin" x-show="loading"></i>
                        <span x-text="loading ? 'Menyimpan...' : 'Simpan Draft'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Jurnal -->
    <div x-show="detailModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="detailModal = false">
        <div x-show="detailModal" x-transition.scale.95 class="w-full max-w-lg bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-zinc-700 flex-shrink-0">
                <div class="flex items-center gap-2"><i data-lucide="eye" class="w-4 h-4 text-amber-500"></i><h3 class="font-semibold text-slate-800 dark:text-white">Detail Jurnal</h3></div>
                <button @click="detailModal = false"><i data-lucide="x" class="w-5 h-5 text-slate-400"></i></button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" x-show="detailData">
                <template x-if="detailData">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div><p class="text-xs text-slate-400 dark:text-zinc-500">Tanggal</p><p class="font-semibold text-slate-700 dark:text-slate-200" x-text="detailData.tanggal"></p></div>
                            <div><p class="text-xs text-slate-400 dark:text-zinc-500">Status</p><p class="font-semibold" x-text="detailData.status"></p></div>
                            <div><p class="text-xs text-slate-400 dark:text-zinc-500">Kelas</p><p class="font-semibold text-slate-700 dark:text-slate-200" x-text="detailData.kelas?.nama"></p></div>
                            <div><p class="text-xs text-slate-400 dark:text-zinc-500">Mapel</p><p class="font-semibold text-slate-700 dark:text-slate-200" x-text="detailData.mapel?.nama"></p></div>
                            <div><p class="text-xs text-slate-400 dark:text-zinc-500">Jam Masuk</p><p class="font-semibold text-slate-700 dark:text-slate-200" x-text="(detailData.jam_masuk_aktual || '-').substring(0,5)"></p></div>
                            <div><p class="text-xs text-slate-400 dark:text-zinc-500">Jam Keluar</p><p class="font-semibold text-slate-700 dark:text-slate-200" x-text="(detailData.jam_keluar_aktual || '-').substring(0,5)"></p></div>
                        </div>
                        <div><p class="text-xs text-slate-400 dark:text-zinc-500 mb-1">Materi</p><p class="text-sm text-slate-700 dark:text-slate-200 whitespace-pre-wrap" x-text="detailData.materi"></p></div>
                        <div x-show="detailData.kendala"><p class="text-xs text-slate-400 dark:text-zinc-500 mb-1">Kendala</p><p class="text-sm text-slate-700 dark:text-slate-200" x-text="detailData.kendala"></p></div>
                        <div x-show="detailData.tindak_lanjut"><p class="text-xs text-slate-400 dark:text-zinc-500 mb-1">Tindak Lanjut</p><p class="text-sm text-slate-700 dark:text-slate-200" x-text="detailData.tindak_lanjut"></p></div>
                        <div x-show="detailData.catatan_validasi" class="p-3 bg-orange-50 dark:bg-orange-950/30 border border-orange-200 dark:border-orange-800/40 rounded-xl">
                            <p class="text-xs font-semibold text-orange-600 dark:text-orange-400 mb-1">Catatan Validasi:</p>
                            <p class="text-sm text-orange-700 dark:text-orange-300" x-text="detailData.catatan_validasi"></p>
                        </div>
                        <div x-show="detailData.lampiran && detailData.lampiran.length > 0">
                            <p class="text-xs text-slate-400 dark:text-zinc-500 mb-2">Lampiran</p>
                            <div class="flex gap-2 flex-wrap">
                                <template x-for="lmp in detailData.lampiran">
                                    <a :href="lmp.url" target="_blank" class="w-20 h-20 rounded-lg overflow-hidden bg-slate-100 dark:bg-zinc-700 flex items-center justify-center hover:opacity-80 transition-opacity">
                                        <img :src="lmp.url" :alt="lmp.keterangan" class="w-full h-full object-cover">
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function jurnalManager() {
    const today = new Date().toISOString().split('T')[0];
    return {
        modal: false, detailModal: false, mode: 'create', loading: false, errorMsg: '', errors: {},
        dragOver: false, previews: [], selectedFiles: [], detailData: null, editId: null,
        today,
        form: {
            jadwal_id: '', kelas_id: '', mapel_id: '', tanggal: today,
            jam_masuk_aktual: '', jam_keluar_aktual: '', materi: '',
            metode_pembelajaran: '', kendala: '', tindak_lanjut: '', catatan: ''
        },

        init() { this.$nextTick(() => lucide.createIcons()); },

        resetForm() {
            this.form = { jadwal_id: '', kelas_id: '', mapel_id: '', tanggal: today, jam_masuk_aktual: '', jam_keluar_aktual: '', materi: '', metode_pembelajaran: '', kendala: '', tindak_lanjut: '', catatan: '' };
            this.errors = {}; this.errorMsg = ''; this.previews = []; this.selectedFiles = [];
            const inp = document.getElementById('lampiran_upload');
            if (inp) inp.value = '';
        },

        openCreate() {
            this.mode = 'create'; this.editId = null;
            this.resetForm();
            this.modal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        openCreateWithJadwal(jadwalId, kelasId, mapelId, jamMulai) {
            this.mode = 'create'; this.editId = null;
            this.resetForm();
            this.form.jadwal_id = String(jadwalId);
            this.form.kelas_id = String(kelasId);
            this.form.mapel_id = String(mapelId);
            this.form.jam_masuk_aktual = jamMulai.substring(0,5);
            this.modal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        async openEdit(id) {
            this.loading = true;
            const res = await fetch(`/guru/jurnal/${id}/show`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            this.loading = false;
            this.mode = 'edit'; this.editId = id;
            this.resetForm();
            this.form = {
                jadwal_id: data.jadwal_id ? String(data.jadwal_id) : '',
                kelas_id: String(data.kelas_id), mapel_id: String(data.mapel_id),
                tanggal: data.tanggal, jam_masuk_aktual: data.jam_masuk_aktual?.substring(0,5) || '',
                jam_keluar_aktual: data.jam_keluar_aktual?.substring(0,5) || '',
                materi: data.materi, metode_pembelajaran: data.metode_pembelajaran || '',
                kendala: data.kendala || '', tindak_lanjut: data.tindak_lanjut || '',
                catatan: data.catatan || ''
            };
            this.modal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        async viewDetail(id) {
            const res = await fetch(`/guru/jurnal/${id}/show`, { headers: { 'Accept': 'application/json' } });
            this.detailData = await res.json();
            this.detailModal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        onJadwalChange() {
            const sel = document.querySelector(`option[value="${this.form.jadwal_id}"]`);
            if (sel && this.form.jadwal_id) {
                this.form.kelas_id = sel.dataset.kelas || '';
                this.form.mapel_id = sel.dataset.mapel || '';
                this.form.jam_masuk_aktual = sel.dataset.mulai?.substring(0,5) || '';
            }
        },

        onFilePick(e) {
            const files = Array.from(e.target.files);
            this.addFiles(files);
        },
        onDrop(e) {
            this.dragOver = false;
            const files = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
            this.addFiles(files);
        },
        addFiles(files) {
            files.slice(0, 5 - this.selectedFiles.length).forEach(f => {
                if (this.selectedFiles.length >= 5) return;
                this.selectedFiles.push(f);
                const reader = new FileReader();
                reader.onload = e => { this.previews.push(e.target.result); this.$nextTick(() => lucide.createIcons()); };
                reader.readAsDataURL(f);
            });
        },
        removeFile(i) {
            this.previews.splice(i, 1);
            this.selectedFiles.splice(i, 1);
        },

        async submitForm() {
            this.loading = true; this.errors = {}; this.errorMsg = '';
            const fd = new FormData();
            Object.entries(this.form).forEach(([k, v]) => { if (v !== '' && v !== null && v !== undefined) fd.append(k, v); });
            this.selectedFiles.forEach(f => fd.append('lampiran[]', f));
            if (this.mode === 'edit') fd.append('_method', 'PUT');

            const url = this.mode === 'create' ? '{{ route('guru.jurnal.store') }}' : `/guru/jurnal/${this.editId}`;
            try {
                const res = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: fd });
                const data = await res.json();
                if (!res.ok) {
                    if (data.errors) this.errors = Object.fromEntries(Object.entries(data.errors).map(([k,v]) => [k,v[0]]));
                    this.errorMsg = data.message || 'Validasi gagal.';
                } else {
                    this.modal = false;
                    Swal.fire({ icon: 'success', title: 'Tersimpan!', text: data.message, timer: 1800, showConfirmButton: false, toast: true, position: 'top-end' });
                    setTimeout(() => location.reload(), 1500);
                }
            } catch(e) { this.errorMsg = 'Gagal terhubung ke server.'; }
            finally { this.loading = false; }
        },

        async submitJurnal(id, tanggal) {
            const { isConfirmed } = await Swal.fire({
                title: 'Submit Jurnal?', text: `Jurnal ${tanggal} akan diajukan untuk validasi Kepala Sekolah.`,
                icon: 'question', showCancelButton: true, confirmButtonText: 'Ya, Submit', cancelButtonText: 'Batal', confirmButtonColor: '#22c55e'
            });
            if (!isConfirmed) return;
            const res = await fetch(`/guru/jurnal/${id}/submit`, { method: 'PATCH', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } });
            const data = await res.json();
            Swal.fire({ icon: 'success', title: 'Disubmit!', text: data.message, timer: 1800, showConfirmButton: false, toast: true, position: 'top-end' });
            setTimeout(() => location.reload(), 1500);
        },

        async deleteJurnal(id) {
            const { isConfirmed } = await Swal.fire({ title: 'Hapus Jurnal?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Hapus', cancelButtonText: 'Batal', confirmButtonColor: '#ef4444' });
            if (!isConfirmed) return;
            const res = await fetch(`/guru/jurnal/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } });
            const data = await res.json();
            Swal.fire({ icon: 'success', title: 'Dihapus!', timer: 1200, showConfirmButton: false, toast: true, position: 'top-end' });
            setTimeout(() => location.reload(), 1000);
        }
    }
}
</script>
@endpush
