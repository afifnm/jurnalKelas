@extends('layouts.app')
@section('title', 'Edit Jurnal')
@section('page-title', 'Edit Jurnal Mengajar')

@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Guru</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <a href="{{ route('guru.jurnal.index') }}" class="hover:text-amber-500 transition-colors">Jurnal Mengajar</a>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <span class="text-slate-700 dark:text-zinc-200 font-medium">Edit Jurnal</span>
@endsection

@section('content')
<div x-data="jurnalEditForm()" x-init="init()" class="max-w-2xl mx-auto">

    <!-- Back + Info -->
    <div class="flex items-center justify-between mb-5">
        <a href="{{ route('guru.jurnal.index') }}"
           class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-700 dark:text-zinc-400 dark:hover:text-zinc-200 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar
        </a>
        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-400 dark:text-zinc-500 bg-slate-100 dark:bg-zinc-800 px-3 py-1.5 rounded-lg">
                <i data-lucide="calendar" class="w-3 h-3 inline mr-1"></i>
                {{ $jurnal->tanggal->translatedFormat('l, d F Y') }}
            </span>
        </div>
    </div>

    <form method="POST" action="{{ route('guru.jurnal.update', $jurnal->id) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <!-- Validation Error Summary -->
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

        <!-- ① Kelas & Mata Pelajaran (tampilan saja) -->
        <div class="card p-5 opacity-80">
            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100 dark:border-zinc-700/50">
                <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-zinc-800 text-slate-400 dark:text-zinc-500 flex items-center justify-center text-xs font-bold flex-shrink-0">1</div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Kelas & Mata Pelajaran</h3>
                    <p class="text-xs text-slate-400 dark:text-zinc-500 flex items-center gap-1"><i data-lucide="lock" class="w-3 h-3"></i> Tidak dapat diubah</p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-slate-400 dark:text-zinc-500 mb-1">Kelas</p>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 input-field bg-slate-50 dark:bg-zinc-800/50 cursor-not-allowed">{{ $jurnal->kelas->nama }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 dark:text-zinc-500 mb-1">Mata Pelajaran</p>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 input-field bg-slate-50 dark:bg-zinc-800/50 cursor-not-allowed">{{ $jurnal->mapel->nama }}</p>
                </div>
            </div>
        </div>

        <!-- ② Waktu Pembelajaran (tampilan saja) -->
        <div class="card p-5 opacity-80">
            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100 dark:border-zinc-700/50">
                <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-zinc-800 text-slate-400 dark:text-zinc-500 flex items-center justify-center text-xs font-bold flex-shrink-0">2</div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Waktu Pembelajaran</h3>
                    <p class="text-xs text-slate-400 dark:text-zinc-500 flex items-center gap-1"><i data-lucide="lock" class="w-3 h-3"></i> Tidak dapat diubah</p>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-slate-400 dark:text-zinc-500 mb-1">Tanggal</p>
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 input-field bg-slate-50 dark:bg-zinc-800/50 cursor-not-allowed">{{ $jurnal->tanggal->translatedFormat('d F Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 dark:text-zinc-500 mb-1 flex items-center gap-1"><i data-lucide="clock" class="w-3 h-3"></i> Jam Mengajar</p>
                    @if($jamSesi)
                    <p class="text-sm font-mono font-semibold text-slate-700 dark:text-slate-200 input-field bg-slate-50 dark:bg-zinc-800/50 cursor-not-allowed">
                        {{ $jamSesi['mulai'] }}–{{ $jamSesi['selesai'] }}
                        @if($jamSesi['jumlah'] > 1)
                        <span class="text-xs font-normal text-slate-400 ml-1">({{ $jamSesi['jumlah'] }} JP)</span>
                        @endif
                    </p>
                    @else
                    <p class="text-sm text-slate-400 dark:text-zinc-500 input-field bg-slate-50 dark:bg-zinc-800/50 cursor-not-allowed">—</p>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-slate-400 dark:text-zinc-500 mb-1">Waktu Input</p>
                    <p class="text-sm font-mono text-slate-700 dark:text-slate-200 input-field bg-slate-50 dark:bg-zinc-800/50 cursor-not-allowed">{{ $jurnal->created_at->format('H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 dark:text-zinc-500 mb-1">Status</p>
                    <p class="text-sm font-semibold input-field bg-slate-50 dark:bg-zinc-800/50 cursor-not-allowed {{ $jurnal->isInputDalamJamMengajar($jamSesi) ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400' }}">
                        {{ $jurnal->isInputDalamJamMengajar($jamSesi) ? '✓ Dalam jam' : '✗ Di luar jam' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- ③ Catatan Mengajar -->
        <div class="card p-5">
            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100 dark:border-zinc-700/50">
                <div class="w-7 h-7 rounded-full bg-amber-100 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs font-bold flex-shrink-0">✎</div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Catatan Mengajar</h3>
                    <p class="text-xs text-slate-400 dark:text-zinc-500">Tuliskan apa yang Anda ajarkan</p>
                </div>
            </div>
            <div class="space-y-4">

                <!-- Materi (wajib) -->
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
                        placeholder="Tuliskan materi yang diajarkan..."
                        required>{{ old('materi', $jurnal->materi) }}</textarea>
                    @error('materi')
                    <p class="text-xs text-red-500 mt-1 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i>{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catatan Tambahan -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                        Catatan Tambahan
                        <span class="text-slate-400 dark:text-zinc-500 font-normal text-xs">(opsional)</span>
                    </label>
                    <textarea name="catatan" rows="3"
                        class="input-field resize-none"
                        placeholder="Contoh: Siswa yang tidak hadir, kendala, atau catatan lainnya...">{{ old('catatan', $jurnal->catatan) }}</textarea>
                    <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1 flex items-center gap-1">
                        <i data-lucide="info" class="w-3 h-3"></i>
                        Bisa diisi nama siswa yang tidak masuk atau informasi lainnya
                    </p>
                </div>
            </div>
        </div>

        <!-- ④ Foto Dokumentasi -->
        <div class="card p-5">
            <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100 dark:border-zinc-700/50">
                <div class="w-7 h-7 rounded-full bg-amber-100 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xs font-bold flex-shrink-0">📷</div>
                <div>
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Foto Dokumentasi KBM</h3>
                    <p class="text-xs text-slate-400 dark:text-zinc-500">Opsional · Maks. 5 foto · JPG/PNG/WEBP · Maks. 5 MB per foto</p>
                </div>
            </div>

            <!-- Foto yang sudah ada -->
            @if($jurnal->lampiran->isNotEmpty())
            <div class="mb-5">
                <p class="text-xs font-medium text-slate-500 dark:text-zinc-400 mb-3 flex items-center gap-1.5">
                    <i data-lucide="images" class="w-3.5 h-3.5"></i>
                    Foto yang Sudah Diunggah ({{ $jurnal->lampiran->count() }})
                </p>
                <div class="grid grid-cols-3 sm:grid-cols-5 gap-3" id="existing-lampiran">
                    @foreach($jurnal->lampiran as $lmp)
                    <div class="relative group" id="lampiran-{{ $lmp->id }}">
                        <a href="{{ $lmp->url }}" target="_blank">
                            <img src="{{ $lmp->url }}" alt="Lampiran"
                                class="w-full aspect-square object-cover rounded-xl border border-slate-200 dark:border-zinc-700 hover:opacity-90 transition-opacity">
                        </a>
                        <button type="button"
                            @click="hapusLampiran({{ $lmp->id }})"
                            class="absolute top-1 right-1 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full
                                   flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow"
                            title="Hapus foto ini">
                            <i data-lucide="trash-2" class="w-3 h-3"></i>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Tambah foto baru -->
            <div>
                <p class="text-xs font-medium text-slate-500 dark:text-zinc-400 mb-2 flex items-center gap-1.5">
                    <i data-lucide="image-plus" class="w-3.5 h-3.5"></i>
                    Tambah Foto Baru
                </p>

                <div @dragover.prevent @drop.prevent="onDrop($event)"
                     @dragenter="dragOver = true" @dragleave="dragOver = false"
                     :class="dragOver ? 'border-amber-400 bg-amber-50 dark:bg-amber-950/20' : 'border-slate-200 dark:border-zinc-700'"
                     class="border-2 border-dashed rounded-xl p-5 text-center transition-colors">
                    <input type="file" id="lampiran_file" name="lampiran[]" multiple accept="image/*"
                        class="hidden" @change="onFilePick($event)">
                    <label for="lampiran_file" class="cursor-pointer block">
                        <i data-lucide="upload" class="w-8 h-8 text-slate-300 dark:text-zinc-600 mx-auto mb-2"></i>
                        <p class="text-sm text-slate-500 dark:text-zinc-400">
                            Seret foto ke sini atau <span class="text-amber-600 dark:text-amber-400 font-medium">klik untuk pilih</span>
                        </p>
                    </label>
                </div>

                <!-- Preview foto baru -->
                <div x-show="previews.length > 0" class="mt-3">
                    <p class="text-xs font-medium text-slate-500 dark:text-zinc-400 mb-2" x-text="`${previews.length} foto baru akan diunggah`"></p>
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
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex items-center gap-3 pb-4">
            <a href="{{ route('guru.jurnal.index') }}"
               class="btn-secondary flex-1 justify-center text-center">
                Batal
            </a>
            <button type="submit" class="btn-primary flex-1 justify-center">
                <i data-lucide="save" class="w-4 h-4"></i>
                Simpan Perubahan
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
function jurnalEditForm() {
    return {
        materi: @json(old('materi', $jurnal->materi)),
        dragOver: false,
        previews: [],
        selectedFiles: [],

        init() {
            this.$nextTick(() => lucide.createIcons());
        },

        async hapusLampiran(id) {
            const { isConfirmed } = await Swal.fire({
                title: 'Hapus foto ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ef4444',
            });
            if (!isConfirmed) return;

            const res = await fetch(`/guru/jurnal/lampiran/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            });

            if (res.ok) {
                const el = document.getElementById(`lampiran-${id}`);
                if (el) el.remove();
                Swal.fire({ icon: 'success', title: 'Foto dihapus', timer: 1200, showConfirmButton: false, toast: true, position: 'top-end' });
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal menghapus foto', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false });
            }
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
