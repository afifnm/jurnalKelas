@extends('layouts.app')
@section('title', 'Isi Jurnal Mengajar')
@section('page-title', 'Isi Jurnal Mengajar')

@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Guru</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <a href="{{ route('guru.jurnal.index') }}" class="hover:text-amber-500 transition-colors">Jurnal Mengajar</a>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <span class="text-slate-700 dark:text-zinc-200 font-medium">Isi Jurnal</span>
@endsection

@section('content')
@php
    // $selectedJadwal = dari URL ?jadwal_id=X (klik manual) — prioritas tertinggi
    // $autoFilledJadwal = dari auto-detect jam sekarang
    $prefill = $selectedJadwal ?? $autoFilledJadwal ?? null;

    // Jam masuk default: manual → jam_mulai, auto → $autoJamMasuk, kosong → ''
    $defaultJamMasuk = old('jam_masuk_aktual',
        $selectedJadwal   ? substr($selectedJadwal->jam_mulai, 0, 5)
        : ($autoFilledJadwal ? $autoJamMasuk : '')
    );

    // Jam keluar default: hanya auto-fill yang mengisi jam keluar
    $defaultJamKeluar = old('jam_keluar_aktual',
        $autoFilledJadwal && !$selectedJadwal ? $autoJamKeluar : ''
    );

    $isAutoFilled = $autoFilledJadwal && !$selectedJadwal && !old('kelas_id');
@endphp
<div x-data="jurnalForm()" x-init="init()" class="max-w-2xl mx-auto">

    <!-- Back + Tanggal -->
    <div class="flex items-center justify-between mb-5">
        <a href="{{ route('guru.jurnal.index') }}"
           class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 dark:text-zinc-400 dark:hover:text-zinc-200 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar
        </a>
        <span class="text-xs text-slate-400 dark:text-zinc-500 bg-slate-100 dark:bg-zinc-800 px-3 py-1.5 rounded-lg">
            <i data-lucide="calendar" class="w-3 h-3 inline mr-1"></i>
            {{ now()->translatedFormat('l, d F Y') }}
        </span>
    </div>

    {{-- Notifikasi auto-fill --}}
    @if($isAutoFilled)
    <div class="flex items-start gap-3 p-4 mb-4 bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-800/40 rounded-2xl">
        <div class="w-8 h-8 rounded-xl bg-green-100 dark:bg-green-900/50 flex items-center justify-center flex-shrink-0">
            <i data-lucide="sparkles" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
        </div>
        <div>
            <p class="text-sm font-semibold text-green-700 dark:text-green-400">Terisi otomatis!</p>
            <p class="text-xs text-green-600 dark:text-green-500 mt-0.5">
                Berdasarkan jadwal mengajar Anda saat ini:
                <span class="font-semibold">{{ $autoFilledJadwal->mapel->nama }}</span>
                di <span class="font-semibold">{{ $autoFilledJadwal->kelas->nama }}</span>
                ({{ substr($autoFilledJadwal->jam_mulai, 0, 5) }}–{{ substr($autoFilledJadwal->jam_selesai, 0, 5) }})
            </p>
            <p class="text-xs text-green-500 dark:text-green-600 mt-1">Periksa kembali dan sesuaikan jika perlu sebelum menyimpan.</p>
        </div>
    </div>
    @endif

    {{-- Jadwal Hari Ini — Pintasan --}}
    @if($jadwalHariIni->isNotEmpty())
    <div class="card p-4 mb-5">
        <p class="text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
            <i data-lucide="zap" class="w-3.5 h-3.5 text-amber-500"></i>
            Pintasan — Pilih Jadwal Hari Ini
        </p>
        <div class="flex flex-wrap gap-2 mb-2">
            @foreach($jadwalHariIni as $j)
            @php $sudah = in_array($j->id, $sudahDiisiHariIni); @endphp
            <button type="button"
                @click="{{ $sudah ? '' : "selectJadwal({$j->id}, {$j->kelas_id}, {$j->mapel_id}, '{$j->jam_mulai}', '{$j->jam_selesai}')" }}"
                :class="selectedJadwalId == '{{ $j->id }}' ? 'ring-2 ring-amber-400 ring-offset-1 dark:ring-offset-zinc-900' : ''"
                class="flex items-center gap-2 px-3 py-2 rounded-xl border text-sm transition-all
                    {{ $sudah
                        ? 'border-green-200 dark:border-green-800/40 bg-green-50 dark:bg-green-950/30 text-green-600 dark:text-green-400 cursor-default opacity-60'
                        : 'border-amber-200 dark:border-amber-800/40 bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-950/50 cursor-pointer' }}">
                <i data-lucide="{{ $sudah ? 'check-circle-2' : 'calendar-plus' }}" class="w-4 h-4 flex-shrink-0"></i>
                <span class="font-medium">{{ $j->mapel->nama }}</span>
                <span class="text-xs opacity-70">{{ $j->kelas->nama }} · {{ substr($j->jam_mulai,0,5) }}–{{ substr($j->jam_selesai,0,5) }}</span>
                @if($sudah)
                <span class="text-[10px] bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 px-1.5 py-0.5 rounded-full font-medium">Sudah diisi</span>
                @endif
            </button>
            @endforeach
        </div>
        <p class="text-xs text-slate-400 dark:text-zinc-600 flex items-center gap-1">
            <i data-lucide="info" class="w-3 h-3"></i>
            Klik jadwal di atas untuk mengisi otomatis kelas, mapel, dan jam secara otomatis
        </p>
    </div>
    @endif

    <form method="POST" action="{{ route('guru.jurnal.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <input type="hidden" name="jadwal_id" x-model="jadwalId">

        {{-- Validation Error Summary --}}
        @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/40 text-red-700 dark:text-red-400 rounded-xl text-sm">
            <i data-lucide="alert-circle" class="w-4 h-4 mt-0.5 flex-shrink-0"></i>
            <div>
                <p class="font-medium mb-1">Mohon perbaiki isian berikut:</p>
                <ul class="list-disc list-inside space-y-0.5 text-xs">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        {{-- ① Kelas & Mata Pelajaran --}}
        <div class="card p-5">
            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100 dark:border-zinc-700/50">
                <div class="w-7 h-7 rounded-full bg-amber-100 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs font-bold flex-shrink-0">1</div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Kelas & Mata Pelajaran</h3>
                    <p class="text-xs text-slate-400 dark:text-zinc-500">Pilih kelas dan mata pelajaran yang diajarkan</p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                        Kelas <span class="text-red-500">*</span>
                    </label>
                    <select name="kelas_id" x-model="kelasId"
                        class="input-field @error('kelas_id') border-red-400 focus:ring-red-400 @enderror" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ old('kelas_id', $prefill?->kelas_id) == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }}
                        </option>
                        @endforeach
                    </select>
                    @error('kelas_id')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                        Mata Pelajaran <span class="text-red-500">*</span>
                    </label>
                    <select name="mapel_id" x-model="mapelId"
                        class="input-field @error('mapel_id') border-red-400 focus:ring-red-400 @enderror" required>
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($mapel as $m)
                        <option value="{{ $m->id }}" {{ old('mapel_id', $prefill?->mapel_id) == $m->id ? 'selected' : '' }}>
                            {{ $m->nama }}
                        </option>
                        @endforeach
                    </select>
                    @error('mapel_id')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ② Waktu Pembelajaran --}}
        <div class="card p-5">
            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100 dark:border-zinc-700/50">
                <div class="w-7 h-7 rounded-full bg-amber-100 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs font-bold flex-shrink-0">2</div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Waktu Pembelajaran</h3>
                    <p class="text-xs text-slate-400 dark:text-zinc-500">Tanggal dan jam masuk/keluar aktual (jam sebenarnya, bukan jadwal)</p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                        Tanggal <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal"
                        value="{{ old('tanggal', today()->toDateString()) }}"
                        max="{{ today()->toDateString() }}"
                        class="input-field @error('tanggal') border-red-400 @enderror" required>
                    @error('tanggal')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                        Jam Masuk Aktual <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="jam_masuk_aktual" x-model="jamMasuk"
                        class="input-field @error('jam_masuk_aktual') border-red-400 @enderror" required>
                    @error('jam_masuk_aktual')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-slate-400 dark:text-zinc-600 mt-1">Jam saat Anda benar-benar masuk kelas</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                        Jam Keluar
                        <span class="text-slate-400 dark:text-zinc-500 font-normal text-xs">(opsional)</span>
                    </label>
                    <input type="time" name="jam_keluar_aktual" x-model="jamKeluar"
                        class="input-field @error('jam_keluar_aktual') border-red-400 @enderror">
                    @error('jam_keluar_aktual')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ③ Catatan Mengajar --}}
        <div class="card p-5">
            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100 dark:border-zinc-700/50">
                <div class="w-7 h-7 rounded-full bg-amber-100 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs font-bold flex-shrink-0">3</div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Catatan Mengajar</h3>
                    <p class="text-xs text-slate-400 dark:text-zinc-500">Tuliskan apa yang Anda ajarkan hari ini</p>
                </div>
            </div>
            <div class="space-y-4">

                {{-- Materi (wajib) --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300">
                            Materi Pembelajaran <span class="text-red-500">*</span>
                        </label>
                        <span x-text="`${materi.length} / 2000`"
                            :class="materi.length > 1800 ? 'text-red-500' : 'text-slate-400 dark:text-zinc-500'"
                            class="text-xs"></span>
                    </div>
                    <textarea name="materi" x-model="materi" rows="4" maxlength="2000"
                        class="input-field resize-y @error('materi') border-red-400 @enderror"
                        placeholder="Contoh: Bab 3 — Persamaan Linear Dua Variabel. Siswa mengerjakan soal latihan halaman 45–48..."
                        required>{{ old('materi') }}</textarea>
                    @error('materi')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>
                    @enderror
                </div>

                {{-- Catatan Tambahan --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                        Catatan Tambahan
                        <span class="text-slate-400 dark:text-zinc-500 font-normal text-xs">(opsional)</span>
                    </label>
                    <textarea name="catatan" rows="3"
                        class="input-field resize-none"
                        placeholder="Contoh: Siswa yang tidak hadir, kendala, atau catatan lainnya...">{{ old('catatan') }}</textarea>
                    <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1 flex items-center gap-1">
                        <i data-lucide="info" class="w-3 h-3"></i>
                        Bisa diisi nama siswa yang tidak masuk atau informasi lainnya
                    </p>
                </div>
            </div>
        </div>

        {{-- ④ Foto Dokumentasi --}}
        <div class="card p-5">
            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100 dark:border-zinc-700/50">
                <div class="w-7 h-7 rounded-full bg-amber-100 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs font-bold flex-shrink-0">4</div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Foto Dokumentasi KBM</h3>
                    <p class="text-xs text-slate-400 dark:text-zinc-500">Opsional · Maks. 5 foto · JPG/PNG/WEBP · Maks. 5 MB per foto</p>
                </div>
            </div>

            {{-- Drop zone --}}
            <div @dragover.prevent @drop.prevent="onDrop($event)"
                 @dragenter="dragOver = true" @dragleave="dragOver = false"
                 :class="dragOver ? 'border-amber-400 bg-amber-50 dark:bg-amber-950/20' : 'border-slate-200 dark:border-zinc-700'"
                 class="border-2 border-dashed rounded-xl p-6 text-center transition-colors">
                <input type="file" id="lampiran_file" name="lampiran[]" multiple accept="image/*"
                    class="hidden" @change="onFilePick($event)">
                <label for="lampiran_file" class="cursor-pointer block">
                    <i data-lucide="image-plus" class="w-10 h-10 text-slate-300 dark:text-zinc-600 mx-auto mb-2"></i>
                    <p class="text-sm text-slate-500 dark:text-zinc-400">
                        Seret foto ke sini atau <span class="text-amber-600 dark:text-amber-400 font-medium">klik untuk pilih</span>
                    </p>
                    <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Foto kegiatan belajar mengajar (KBM)</p>
                </label>
            </div>

            {{-- Preview foto yang dipilih --}}
            <div x-show="previews.length > 0" class="mt-4">
                <p class="text-xs font-medium text-slate-500 dark:text-zinc-400 mb-2" x-text="`${previews.length} foto dipilih`"></p>
                <div class="grid grid-cols-3 sm:grid-cols-5 gap-3">
                    <template x-for="(p, i) in previews" :key="i">
                        <div class="relative group">
                            <img :src="p" class="w-full aspect-square object-cover rounded-xl border border-slate-200 dark:border-zinc-700">
                            <button type="button" @click="removeFile(i)"
                                class="absolute top-1 right-1 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full
                                       flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow">
                                <i data-lucide="x" class="w-3 h-3"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <p x-show="previews.length >= 5"
               class="text-xs text-amber-600 dark:text-amber-400 mt-3 flex items-center gap-1.5">
                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                Sudah mencapai batas maksimal 5 foto
            </p>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex items-center gap-3 pb-4">
            <a href="{{ route('guru.jurnal.index') }}"
               class="btn-secondary flex-1 justify-center text-center">
                Batal
            </a>
            <button type="button" @click="handleSubmit($el.closest('form'))" class="btn-primary flex-1 justify-center">
                <i data-lucide="save" class="w-4 h-4"></i>
                Simpan Jurnal
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
function jurnalForm() {
    return {
        // Diisi dari server (auto-fill atau manual selection atau old() setelah error validasi)
        jadwalId:         @json(old('jadwal_id', $prefill?->id ?? '')),
        kelasId:          @json(old('kelas_id',  $prefill?->kelas_id  ?? '')),
        mapelId:          @json(old('mapel_id',  $prefill?->mapel_id  ?? '')),
        jamMasuk:         @json($defaultJamMasuk),
        jamKeluar:        @json($defaultJamKeluar),
        materi:           @json(old('materi', '')),
        selectedJadwalId: @json($prefill?->id ?? ''),
        jurnalHariIni:    @json($jurnalHariIni->map(fn($j) => ['id' => $j->id, 'kelas_id' => (string)$j->kelas_id, 'mapel_id' => (string)$j->mapel_id])),

        dragOver: false,
        previews: [],
        selectedFiles: [],

        init() {
            // Konversi ke string agar x-model select cocok dengan value="" option
            this.jadwalId = String(this.jadwalId ?? '');
            this.kelasId  = String(this.kelasId  ?? '');
            this.mapelId  = String(this.mapelId  ?? '');
            this.selectedJadwalId = String(this.selectedJadwalId ?? '');
            this.$nextTick(() => lucide.createIcons());
        },

        // Dipanggil saat guru klik salah satu pintasan jadwal
        selectJadwal(jadwalId, kelasId, mapelId, jamMulai, jamSelesai) {
            this.jadwalId         = String(jadwalId);
            this.kelasId          = String(kelasId);
            this.mapelId          = String(mapelId);
            this.jamMasuk         = jamMulai.substring(0, 5);
            this.jamKeluar        = jamSelesai ? jamSelesai.substring(0, 5) : '';
            this.selectedJadwalId = String(jadwalId);
        },

        handleSubmit(form) {
            const kelas = String(this.kelasId);
            const mapel = String(this.mapelId);

            if (!kelas || !mapel) {
                form.requestSubmit();
                return;
            }

            const duplikat = this.jurnalHariIni.find(j => j.kelas_id === kelas && j.mapel_id === mapel);
            if (duplikat) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Jurnal sudah diisi!',
                    html: 'Jurnal untuk kelas dan mata pelajaran ini <strong>sudah pernah diisi hari ini</strong>.<br><br>Jika ingin mengubah, silakan <strong>edit</strong> atau <strong>hapus</strong> jurnal sebelumnya terlebih dahulu.',
                    confirmButtonText: 'Lihat Jurnal',
                    showCancelButton: true,
                    cancelButtonText: 'Tutup',
                    confirmButtonColor: '#f59e0b',
                }).then(result => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route('guru.jurnal.index') }}';
                    }
                });
                return;
            }

            form.requestSubmit();
        },

        onFilePick(e) {
            this.addFiles(Array.from(e.target.files));
        },
        onDrop(e) {
            this.dragOver = false;
            this.addFiles(Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/')));
        },
        addFiles(files) {
            files.slice(0, 5 - this.selectedFiles.length).forEach(f => {
                if (this.selectedFiles.length >= 5) return;
                this.selectedFiles.push(f);
                const reader = new FileReader();
                reader.onload = e => {
                    this.previews.push(e.target.result);
                    this.$nextTick(() => lucide.createIcons());
                };
                reader.readAsDataURL(f);
            });
            this.syncFileInput();
        },
        removeFile(i) {
            this.previews.splice(i, 1);
            this.selectedFiles.splice(i, 1);
            this.syncFileInput();
        },
        syncFileInput() {
            const dt = new DataTransfer();
            this.selectedFiles.forEach(f => dt.items.add(f));
            const inp = document.getElementById('lampiran_file');
            if (inp) inp.files = dt.files;
        },
    }
}
</script>
@endpush
