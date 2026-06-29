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
    $isDisabled = $jadwalHariIni->isEmpty();
    $prefill = $selectedJadwal ?? $autoFilledJadwal ?? null;
    $isAutoFilled = $autoFilledJadwal && !$selectedJadwal && !old('kelas_id');
    $prefillGrup = $prefill ? $grupJadwalHariIni->first(fn($g) => in_array($prefill->id, $g['ids'])) : null;
    $prefillJamMulai  = $prefill ? substr($prefill->jamPelajaran->jam_mulai ?? '', 0, 5) : '';
    $prefillJamSelesai = $prefillGrup ? substr($prefillGrup['jadwal']->last()->jamPelajaran->jam_selesai, 0, 5) : ($prefill ? substr($prefill->jamPelajaran->jam_selesai ?? '', 0, 5) : '');
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

    {{-- Notifikasi Tidak Ada Jadwal --}}
    @if($jadwalHariIni->isEmpty())
    <div class="flex items-start gap-3 p-4 mb-5 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800/40 rounded-2xl">
        <div class="w-8 h-8 rounded-xl bg-red-100 dark:bg-red-900/50 flex items-center justify-center flex-shrink-0">
            <i data-lucide="calendar-x" class="w-4 h-4 text-red-600 dark:text-red-400"></i>
        </div>
        <div>
            <p class="text-sm font-semibold text-red-700 dark:text-red-400">Bapak/Ibu tidak ada jadwal hari ini!</p>
            <p class="text-xs text-red-600 dark:text-red-500 mt-0.5">
                Sistem melihat bahwa Bapak/Ibu tidak memiliki jadwal mengajar pada hari ini. Oleh karena itu, form pengisian jurnal ditutup dan tidak bisa diisi.
            </p>
        </div>
    </div>
    @endif

    {{-- Notifikasi auto-fill --}}
    @if($isAutoFilled && $jadwalHariIni->isNotEmpty())
    @php
        $autoGrup = $grupJadwalHariIni->first(fn($g) => $g['jadwal']->first()->id === $autoFilledJadwal->id);
        $autoJamSelesaiGrup = $autoGrup ? substr($autoGrup['jadwal']->last()->jamPelajaran->jam_selesai, 0, 5) : substr($autoFilledJadwal->jamPelajaran->jam_selesai, 0, 5);
    @endphp
    <div class="flex items-start gap-3 p-4 mb-4 bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-800/40 rounded-2xl">
        <div class="w-8 h-8 rounded-xl bg-green-100 dark:bg-green-900/50 flex items-center justify-center flex-shrink-0">
            <i data-lucide="sparkles" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
        </div>
        <div>
            <p class="text-sm font-semibold text-green-700 dark:text-green-400">Sudah terisi otomatis!</p>
            <p class="text-xs text-green-600 dark:text-green-500 mt-0.5">
                Berdasarkan jam sekarang, jadwal Bapak/Ibu adalah:
                <span class="font-semibold">{{ $autoFilledJadwal->mapel->nama }}</span>
                di kelas <span class="font-semibold">{{ $autoFilledJadwal->kelas->nama }}</span>
                ({{ substr($autoFilledJadwal->jamPelajaran->jam_mulai, 0, 5) }}–{{ $autoJamSelesaiGrup }})
            </p>
            <p class="text-xs text-green-500 dark:text-green-600 mt-1">Bapak/Ibu bisa langsung lompat ke Langkah 2 untuk menulis materi, lalu tekan SIMPAN.</p>
        </div>
    </div>
    @endif

    {{-- Jadwal Hari Ini — Pintasan --}}
    @if($grupJadwalHariIni->isNotEmpty())
    <div class="card p-4 mb-5">
        <p class="text-xs font-bold text-amber-600 dark:text-amber-500 uppercase tracking-wider mb-3 flex items-center gap-1.5 bg-amber-100 dark:bg-amber-900/40 p-2 rounded-lg w-fit">
            <i data-lucide="hand-pointer" class="w-4 h-4"></i>
            KLIK KOTAK JADWAL DI BAWAH INI BIAR OTOMATIS
        </p>
        <div class="flex flex-wrap gap-2 mb-2">
            @foreach($grupJadwalHariIni as $grup)
            @php
                $first      = $grup['jadwal']->first();
                $last       = $grup['jadwal']->last();
                $sudah      = count(array_intersect($grup['ids'], $sudahDiisiHariIni)) > 0;
                $jamMulai   = $first->jamPelajaran->jam_mulai;
                $jamSelesai = $last->jamPelajaran->jam_selesai;
                $jumlahJam  = $grup['jadwal']->count();
            @endphp
            <button type="button"
                @click="{{ $sudah ? '' : "selectJadwal({$first->id}, {$first->kelas_id}, {$first->mapel_id}, '{$jamMulai}', '{$jamSelesai}')" }}"
                :class="selectedJadwalId == '{{ $first->id }}' ? 'ring-4 ring-amber-500 ring-offset-2 dark:ring-offset-zinc-900 scale-[1.02] shadow-lg' : ''"
                class="flex items-center gap-3 px-4 py-3 rounded-2xl border-2 text-left transition-all w-full sm:w-auto
                    {{ $sudah
                        ? 'border-green-300 dark:border-green-800/40 bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-400 cursor-default opacity-70'
                        : 'border-amber-400 dark:border-amber-600 bg-amber-100 dark:bg-amber-950/50 text-amber-900 dark:text-amber-300 hover:bg-amber-200 dark:hover:bg-amber-900/60 hover:border-amber-500 cursor-pointer shadow-sm' }}">
                <i data-lucide="{{ $sudah ? 'check-circle-2' : 'calendar-plus' }}" class="w-6 h-6 flex-shrink-0 {{ $sudah ? 'text-green-600' : 'text-amber-600' }}"></i>
                <div>
                    <span class="block font-bold text-base">{{ $first->mapel->nama }}</span>
                    <span class="block text-sm opacity-90">Kelas {{ $first->kelas->nama }} | {{ substr($jamMulai,0,5) }}–{{ substr($jamSelesai,0,5) }}</span>
                </div>
                <div class="flex flex-col gap-1 items-end ml-auto sm:ml-4">
                    @if($jumlahJam > 1)
                    <span class="text-xs bg-amber-300 dark:bg-amber-800 text-amber-900 dark:text-amber-200 px-2 py-1 rounded-md font-bold">{{ $jumlahJam }} Jam</span>
                    @endif
                    @if($sudah)
                    <span class="text-xs bg-green-200 dark:bg-green-800 text-green-800 dark:text-green-200 px-2 py-1 rounded-md font-bold">Sudah Diisi</span>
                    @endif
                </div>
            </button>
            @endforeach
        </div>
        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-2 flex items-center gap-1 font-medium">
            <i data-lucide="info" class="w-4 h-4 text-sky-500"></i>
            Tips: Cukup klik salah satu kotak di atas, maka Kelas dan Mata Pelajaran di bawah ini akan terisi sendiri.
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
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Langkah 1: Pastikan Kelas & Mapel Benar</h3>
                    <p class="text-xs text-slate-400 dark:text-zinc-500">Cek kembali kelas dan mata pelajaran yang diajarkan.</p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                        Kelas <span class="text-red-500">*</span>
                    </label>
                    <input type="hidden" name="kelas_id" :value="kelasId">
                    <input type="text"
                        readonly
                        :value="kelasId ? kelasData[kelasId] : ''"
                        placeholder="-- Otomatis terisi --"
                        class="input-field bg-slate-50 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 font-semibold cursor-not-allowed @error('kelas_id') border-red-400 @enderror">
                    @error('kelas_id')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                        Mata Pelajaran <span class="text-red-500">*</span>
                    </label>
                    <input type="hidden" name="mapel_id" :value="mapelId">
                    <input type="text"
                        readonly
                        :value="mapelId ? mapelData[mapelId] : ''"
                        placeholder="-- Otomatis terisi --"
                        class="input-field bg-slate-50 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 font-semibold cursor-not-allowed @error('mapel_id') border-red-400 @enderror">
                    @error('mapel_id')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>
                    @enderror
                </div>
            </div>
            {{-- Info jam jadwal (muncul jika pintasan dipilih) --}}
            <div x-show="jamMulai" x-cloak class="mt-4 flex items-center gap-2 px-3 py-2 rounded-lg bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/40 text-amber-700 dark:text-amber-400 text-sm">
                <i data-lucide="clock" class="w-4 h-4 flex-shrink-0"></i>
                <span>Jam mengajar: <strong x-text="jamMulai + '–' + jamSelesai"></strong></span>
            </div>

            {{-- Tanggal di dalam step 1 --}}
            <div class="mt-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                    Tanggal <span class="text-red-500">*</span>
                </label>
                <input type="date" name="tanggal"
                    value="{{ old('tanggal', today()->toDateString()) }}"
                    class="input-field bg-slate-50 dark:bg-zinc-800 text-slate-500 cursor-not-allowed @error('tanggal') border-red-400 @enderror" readonly required>
                @error('tanggal')
                <p class="text-xs text-red-500 mt-1 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- ② Catatan Mengajar --}}
        <div class="card p-5">
            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100 dark:border-zinc-700/50">
                <div class="w-7 h-7 rounded-full bg-amber-100 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs font-bold flex-shrink-0">2</div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Langkah 2: Tulis Materi & Catatan</h3>
                    <p class="text-xs text-slate-400 dark:text-zinc-500">Ketik materi apa yang diajarkan hari ini</p>
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
                        required @disabled($isDisabled)>{{ old('materi') }}</textarea>
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
                        placeholder="Contoh: Siswa yang tidak hadir, kendala, atau catatan lainnya..."
                        @disabled($isDisabled)>{{ old('catatan') }}</textarea>
                    <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1 flex items-center gap-1">
                        <i data-lucide="info" class="w-3 h-3"></i>
                        Bisa diisi nama siswa yang tidak masuk atau informasi lainnya
                    </p>
                </div>
            </div>
        </div>

        {{-- ③ Foto Dokumentasi --}}
        <div class="card p-5">
            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100 dark:border-zinc-700/50">
                <div class="w-7 h-7 rounded-full bg-amber-100 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs font-bold flex-shrink-0">3</div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Langkah 3: Masukkan Foto (Jika Ada)</h3>
                    <p class="text-xs text-slate-400 dark:text-zinc-500">Boleh dikosongkan. Maksimal 5 foto.</p>
                </div>
            </div>

            {{-- Drop zone --}}
            <div @if(!$isDisabled) @dragover.prevent @drop.prevent="onDrop($event)"
                 @dragenter="dragOver = true" @dragleave="dragOver = false" @endif
                 :class="dragOver ? 'border-amber-400 bg-amber-50 dark:bg-amber-950/20' : 'border-slate-200 dark:border-zinc-700'"
                 class="border-2 border-dashed rounded-xl p-6 text-center transition-colors {{ $isDisabled ? 'opacity-60 cursor-not-allowed bg-slate-50 dark:bg-zinc-800/50 pointer-events-none' : '' }}">
                <input type="file" id="lampiran_file" name="lampiran[]" multiple accept="image/*"
                    class="hidden" @change="onFilePick($event)" @disabled($isDisabled)>
                <label for="lampiran_file" class="block {{ $isDisabled ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                    <i data-lucide="image-plus" class="w-10 h-10 text-slate-300 dark:text-zinc-600 mx-auto mb-2"></i>
                    <p class="text-sm text-slate-500 dark:text-zinc-400">
                        <span class="text-amber-600 dark:text-amber-400 font-medium">Klik di sini</span> untuk memilih foto dari HP/Laptop
                    </p>
                    <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1">Foto bukti kegiatan belajar mengajar di kelas</p>
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
        <div class="space-y-2 pb-4">
            <button type="button" @click="handleSubmit($el.closest('form'))" @disabled($isDisabled)
                class="flex items-center justify-center gap-2 w-full py-4 rounded-2xl bg-amber-400 hover:bg-amber-500 active:scale-[.98] text-zinc-900 font-bold text-base transition-all shadow-md shadow-amber-200 dark:shadow-none disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-amber-400 disabled:active:scale-100">
                <i data-lucide="{{ $isDisabled ? 'lock' : 'save' }}" class="w-5 h-5"></i>
                {{ $isDisabled ? 'TIDAK BISA ISI JURNAL HARI INI' : 'SIMPAN JURNAL SEKARANG' }}
            </button>
            <a href="{{ route('guru.jurnal.index') }}"
               class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-200 text-sm transition-colors">
                Batal / Kembali
            </a>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
function jurnalForm() {
    return {
        jadwalId:         @json(old('jadwal_id', $prefill?->id ?? '')),
        kelasId:          @json(old('kelas_id',  $prefill?->kelas_id  ?? '')),
        mapelId:          @json(old('mapel_id',  $prefill?->mapel_id  ?? '')),
        materi:           @json(old('materi', '')),
        selectedJadwalId: @json($prefill?->id ?? ''),
        jamMulai:         @json($prefillJamMulai),
        jamSelesai:       @json($prefillJamSelesai),
        jurnalHariIni:    @json($jurnalHariIni->map(fn($j) => ['id' => $j->id, 'kelas_id' => (string)$j->kelas_id, 'mapel_id' => (string)$j->mapel_id])),
        kelasData:        @json(collect($kelas)->pluck('nama', 'id')),
        mapelData:        @json(collect($mapel)->pluck('nama', 'id')),

        dragOver: false,
        previews: [],
        selectedFiles: [],

        init() {
            this.jadwalId         = String(this.jadwalId  ?? '');
            this.kelasId          = String(this.kelasId   ?? '');
            this.mapelId          = String(this.mapelId   ?? '');
            this.selectedJadwalId = String(this.selectedJadwalId ?? '');
            this.jamMulai         = this.jamMulai   ?? '';
            this.jamSelesai       = this.jamSelesai ?? '';

            this.$nextTick(() => {
                lucide.createIcons();
            });
        },

        selectJadwal(jadwalId, kelasId, mapelId, jamMulai, jamSelesai) {
            this.jadwalId         = String(jadwalId);
            this.kelasId          = String(kelasId);
            this.mapelId          = String(mapelId);
            this.selectedJadwalId = String(jadwalId);
            this.jamMulai         = jamMulai  ? jamMulai.substring(0, 5)  : '';
            this.jamSelesai       = jamSelesai ? jamSelesai.substring(0, 5) : '';
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
                const reader = new FileReader();
                reader.onload = e => {
                    const img = new Image();
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        const MAX_SIZE = 1200;
                        let width = img.width;
                        let height = img.height;
                        if (width > height && width > MAX_SIZE) {
                            height *= MAX_SIZE / width;
                            width = MAX_SIZE;
                        } else if (height > MAX_SIZE) {
                            width *= MAX_SIZE / height;
                            height = MAX_SIZE;
                        }
                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        canvas.toBlob(blob => {
                            if (!blob) return;
                            const newFile = new File([blob], f.name.replace(/\.[^/.]+$/, "") + ".webp", {
                                type: 'image/webp',
                                lastModified: Date.now()
                            });
                            this.selectedFiles.push(newFile);
                            this.previews.push(canvas.toDataURL('image/webp', 0.8));
                            this.syncFileInput();
                            this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
                        }, 'image/webp', 0.8);
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(f);
            });
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
