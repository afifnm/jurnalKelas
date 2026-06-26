@extends('layouts.app')
@section('title', 'Identitas Sekolah')
@section('page-title', 'Identitas Sekolah')
@section('content')
<div x-data="sekolahManager()" x-init="init()">
    <div class="max-w-2xl">
        <div class="mb-5">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">Identitas Sekolah</h2>
            <p class="text-sm text-slate-400 dark:text-zinc-500">Informasi sekolah yang tampil pada laporan dan header aplikasi</p>
        </div>

        <div class="card p-6">
            <form @submit.prevent="submitForm()" class="space-y-5">
                @csrf @method('PUT')


                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="building-2" class="w-3.5 h-3.5 inline mr-1"></i>Nama Sekolah</label>
                        <input type="text" name="nama" value="{{ $sekolah?->nama }}" class="input-field">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="landmark" class="w-3.5 h-3.5 inline mr-1"></i>Nama Yayasan</label>
                        <input type="text" name="nama_yayasan" value="{{ $sekolah?->nama_yayasan }}" class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="hash" class="w-3.5 h-3.5 inline mr-1"></i>NPSN</label>
                        <input type="text" name="npsn" value="{{ $sekolah?->npsn }}" class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="user-tie" class="w-3.5 h-3.5 inline mr-1"></i>Kepala Sekolah</label>
                        <input type="text" name="kepala_sekolah" value="{{ $sekolah?->kepala_sekolah }}" class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="phone" class="w-3.5 h-3.5 inline mr-1"></i>Telepon</label>
                        <input type="text" name="telepon" value="{{ $sekolah?->telepon }}" class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="mail" class="w-3.5 h-3.5 inline mr-1"></i>Email</label>
                        <input type="email" name="email" value="{{ $sekolah?->email }}" class="input-field">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="map-pin" class="w-3.5 h-3.5 inline mr-1"></i>Alamat</label>
                        <textarea name="alamat" rows="3" class="input-field resize-none">{{ $sekolah?->alamat }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5"><i data-lucide="globe" class="w-3.5 h-3.5 inline mr-1"></i>Website</label>
                        <input type="text" name="website" value="{{ $sekolah?->website }}" class="input-field">
                    </div>
                </div>

                <p x-show="errorMsg" x-text="errorMsg" class="text-xs text-red-500"></p>

                <div class="flex justify-end pt-2">
                    <button type="submit" :disabled="loading" class="btn-primary">
                        <i data-lucide="save" class="w-4 h-4" x-show="!loading"></i>
                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin" x-show="loading"></i>
                        <span x-text="loading ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function sekolahManager() {
    return {
        loading: false, errorMsg: '',
        init() { lucide.createIcons(); },
        async submitForm() {
            this.loading = true; this.errorMsg = '';
            const form = document.querySelector('form');
            const fd = new FormData(form);
            fd.append('_method', 'PUT');
            try {
                const res = await fetch('{{ route('admin.sekolah.update') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: fd });
                const data = await res.json();
                if (!res.ok) { this.errorMsg = data.message || 'Terjadi kesalahan.'; }
                else { Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1800, showConfirmButton: false, toast: true, position: 'top-end' }); }
            } catch { this.errorMsg = 'Gagal terhubung.'; }
            finally { this.loading = false; }
        }
    }
}
</script>
@endpush
