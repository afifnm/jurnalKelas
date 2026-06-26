@extends('layouts.app')
@section('title', 'Validasi Jurnal')
@section('page-title', 'Validasi Jurnal')

@section('content')
<div x-data="validasiManager()" x-init="init()">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">Validasi Jurnal</h2>
            <p class="text-sm text-slate-400 dark:text-zinc-500">Periksa dan validasi jurnal mengajar guru</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="card p-4 mb-4">
        <form method="GET" class="flex flex-wrap gap-3">
            <select name="guru_id" class="input-field w-auto text-sm">
                <option value="">Semua Guru</option>
                @foreach($guru as $g)
                    <option value="{{ $g->id }}" @selected(request('guru_id') == $g->id)>{{ $g->nama }}</option>
                @endforeach
            </select>
            <select name="kelas_id" class="input-field w-auto text-sm">
                <option value="">Semua Kelas</option>
                @foreach($kelas as $k)
                    <option value="{{ $k->id }}" @selected(request('kelas_id') == $k->id)>{{ $k->nama }}</option>
                @endforeach
            </select>
            <select name="status" class="input-field w-auto text-sm">
                <option value="">Submitted/Validated/Revisi</option>
                <option value="submitted" @selected(request('status') === 'submitted')>Menunggu Validasi</option>
                <option value="validated" @selected(request('status') === 'validated')>Tervalidasi</option>
                <option value="revisi" @selected(request('status') === 'revisi')>Perlu Revisi</option>
            </select>
            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="input-field w-auto text-sm" placeholder="Dari">
            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="input-field w-auto text-sm" placeholder="Sampai">
            <button type="submit" class="btn-primary text-sm"><i data-lucide="filter" class="w-4 h-4"></i> Filter</button>
            @if(request()->hasAny(['guru_id','kelas_id','status','tanggal_dari','tanggal_sampai']))
            <a href="{{ route('ks.jurnal.index') }}" class="btn-secondary text-sm"><i data-lucide="x" class="w-4 h-4"></i> Reset</a>
            @endif
        </form>
    </div>

    <!-- Tabel -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-zinc-800/60 border-b border-slate-200 dark:border-zinc-700/50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Guru</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Kelas / Mapel</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Keterlambatan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-700/50">
                    @forelse($jurnal as $j)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center text-amber-600 dark:text-amber-400 text-xs font-bold">
                                    {{ strtoupper(substr($j->guru->nama, 0, 1)) }}
                                </div>
                                <span class="font-medium text-slate-700 dark:text-slate-200">{{ $j->guru->nama }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="font-medium text-slate-700 dark:text-slate-200">{{ $j->kelas->nama }}</p>
                            <p class="text-xs text-slate-400">{{ $j->mapel->nama }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="font-semibold text-slate-700 dark:text-slate-200">{{ $j->tanggal->format('d M Y') }}</p>
                            <p class="text-xs text-slate-400">{{ $j->jam_masuk_aktual ? substr($j->jam_masuk_aktual, 0, 5) : '-' }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            @if($j->is_terlambat)
                                <span class="badge bg-red-100 dark:bg-red-950/40 text-red-700 dark:text-red-400">
                                    <i data-lucide="clock-alert" class="w-3 h-3"></i>
                                    +{{ $j->menit_terlambat }} mnt
                                </span>
                            @else
                                <span class="badge badge-validated">
                                    <i data-lucide="clock-check" class="w-3 h-3"></i>
                                    Tepat waktu
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="badge {{ $j->badge_color }}">{{ $j->status_label }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end gap-1.5">
                                <button @click="viewDetail({{ $j->id }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/30 rounded-lg transition-colors">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail
                                </button>
                                @if($j->status === 'submitted')
                                <button @click="openValidasi({{ $j->id }}, '{{ addslashes($j->guru->nama) }}')"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-950/30 rounded-lg transition-colors">
                                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> Validasi
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center">
                        <div class="flex flex-col items-center text-slate-400 dark:text-zinc-600">
                            <i data-lucide="inbox" class="w-10 h-10 mb-2 opacity-50"></i>
                            <p class="text-sm">Tidak ada jurnal</p>
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

    <!-- Modal Detail -->
    <div x-show="detailModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="detailModal = false">
        <div x-show="detailModal" x-transition.scale.95 class="w-full max-w-xl bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-zinc-700 flex-shrink-0">
                <div class="flex items-center gap-2"><i data-lucide="notebook" class="w-4 h-4 text-amber-500"></i><h3 class="font-semibold text-slate-800 dark:text-white">Detail Jurnal</h3></div>
                <button @click="detailModal = false"><i data-lucide="x" class="w-5 h-5 text-slate-400"></i></button>
            </div>
            <div class="flex-1 overflow-y-auto p-6">
                <template x-if="detailData">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div><p class="text-xs text-slate-400 dark:text-zinc-500">Guru</p><p class="font-semibold text-slate-700 dark:text-slate-200" x-text="detailData.guru?.nama"></p></div>
                            <div><p class="text-xs text-slate-400 dark:text-zinc-500">Tanggal</p><p class="font-semibold text-slate-700 dark:text-slate-200" x-text="detailData.tanggal"></p></div>
                            <div><p class="text-xs text-slate-400 dark:text-zinc-500">Kelas</p><p class="font-semibold text-slate-700 dark:text-slate-200" x-text="detailData.kelas?.nama"></p></div>
                            <div><p class="text-xs text-slate-400 dark:text-zinc-500">Mapel</p><p class="font-semibold text-slate-700 dark:text-slate-200" x-text="detailData.mapel?.nama"></p></div>
                            <div><p class="text-xs text-slate-400 dark:text-zinc-500">Jam Masuk</p><p class="font-semibold text-slate-700 dark:text-slate-200" x-text="(detailData.jam_masuk_aktual || '-').substring(0,5)"></p></div>
                            <div><p class="text-xs text-slate-400 dark:text-zinc-500">Keterlambatan</p>
                                <p class="font-semibold" :class="detailData.is_terlambat ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'"
                                   x-text="detailData.is_terlambat ? '+' + detailData.menit_terlambat + ' menit' : 'Tepat waktu'"></p></div>
                        </div>
                        <div class="border-t border-slate-100 dark:border-zinc-700/50 pt-3">
                            <p class="text-xs text-slate-400 dark:text-zinc-500 mb-1">Materi</p>
                            <p class="text-sm text-slate-700 dark:text-slate-200 whitespace-pre-wrap" x-text="detailData.materi"></p>
                        </div>
                        <div x-show="detailData.metode_pembelajaran"><p class="text-xs text-slate-400 dark:text-zinc-500 mb-1">Metode</p><p class="text-sm text-slate-700 dark:text-slate-200" x-text="detailData.metode_pembelajaran"></p></div>
                        <div x-show="detailData.kendala"><p class="text-xs text-slate-400 dark:text-zinc-500 mb-1">Kendala</p><p class="text-sm text-slate-700 dark:text-slate-200" x-text="detailData.kendala"></p></div>
                        <div x-show="detailData.tindak_lanjut"><p class="text-xs text-slate-400 dark:text-zinc-500 mb-1">Tindak Lanjut</p><p class="text-sm text-slate-700 dark:text-slate-200" x-text="detailData.tindak_lanjut"></p></div>
                        <div x-show="detailData.lampiran && detailData.lampiran.length > 0">
                            <p class="text-xs text-slate-400 dark:text-zinc-500 mb-2">Lampiran</p>
                            <div class="flex gap-2 flex-wrap">
                                <template x-for="lmp in detailData.lampiran">
                                    <a :href="lmp.url" target="_blank" class="w-20 h-20 rounded-lg overflow-hidden bg-slate-100 dark:bg-zinc-700 hover:opacity-80 transition-opacity">
                                        <img :src="lmp.url" class="w-full h-full object-cover">
                                    </a>
                                </template>
                            </div>
                        </div>
                        <!-- Tombol Validasi di modal detail -->
                        <div x-show="detailData.status === 'submitted'" class="pt-3 border-t border-slate-100 dark:border-zinc-700/50 flex gap-3">
                            <button @click="detailModal = false; openValidasi(detailData.id, detailData.guru?.nama)"
                                class="btn-primary flex-1"><i data-lucide="shield-check" class="w-4 h-4"></i> Validasi Jurnal Ini</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Modal Validasi -->
    <div x-show="validasiModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="validasiModal = false">
        <div x-show="validasiModal" x-transition.scale.95 class="w-full max-w-md bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-zinc-700">
                <div class="flex items-center gap-2"><i data-lucide="shield-check" class="w-4 h-4 text-amber-500"></i><h3 class="font-semibold text-slate-800 dark:text-white">Validasi Jurnal</h3></div>
                <button @click="validasiModal = false"><i data-lucide="x" class="w-5 h-5 text-slate-400"></i></button>
            </div>
            <div class="p-6 space-y-4">
                <p class="text-sm text-slate-600 dark:text-zinc-400">Validasi jurnal dari: <strong x-text="validasiGuruNama" class="text-slate-700 dark:text-slate-200"></strong></p>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-2">Keputusan</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all"
                            :class="aksi === 'validated' ? 'border-green-400 bg-green-50 dark:bg-green-950/30' : 'border-slate-200 dark:border-zinc-700'">
                            <input type="radio" x-model="aksi" value="validated" class="hidden">
                            <i data-lucide="check-circle-2" class="w-5 h-5" :class="aksi === 'validated' ? 'text-green-600' : 'text-slate-300'"></i>
                            <span class="text-sm font-medium" :class="aksi === 'validated' ? 'text-green-700 dark:text-green-400' : 'text-slate-500 dark:text-zinc-500'">Setujui</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all"
                            :class="aksi === 'revisi' ? 'border-orange-400 bg-orange-50 dark:bg-orange-950/30' : 'border-slate-200 dark:border-zinc-700'">
                            <input type="radio" x-model="aksi" value="revisi" class="hidden">
                            <i data-lucide="rotate-ccw" class="w-5 h-5" :class="aksi === 'revisi' ? 'text-orange-600' : 'text-slate-300'"></i>
                            <span class="text-sm font-medium" :class="aksi === 'revisi' ? 'text-orange-700 dark:text-orange-400' : 'text-slate-500 dark:text-zinc-500'">Minta Revisi</span>
                        </label>
                    </div>
                </div>
                <div x-show="aksi === 'revisi'" x-transition>
                    <label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                        <i data-lucide="message-square" class="w-3.5 h-3.5 inline mr-1"></i>Catatan untuk Guru
                    </label>
                    <textarea x-model="catatanValidasi" rows="3" class="input-field resize-none" placeholder="Tuliskan hal yang perlu direvisi..."></textarea>
                    <p x-show="validasiError" x-text="validasiError" class="text-xs text-red-500 mt-1"></p>
                </div>
                <div class="flex gap-3 pt-2">
                    <button @click="validasiModal = false" class="btn-secondary flex-1">Batal</button>
                    <button @click="kirimValidasi()" :disabled="!aksi || validasiLoading" class="btn-primary flex-1"
                        :class="aksi === 'validated' ? 'bg-green-500 hover:bg-green-600' : (aksi === 'revisi' ? 'bg-orange-500 hover:bg-orange-600' : '')">
                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin" x-show="validasiLoading"></i>
                        <i data-lucide="send" class="w-4 h-4" x-show="!validasiLoading"></i>
                        <span x-text="validasiLoading ? 'Memproses...' : (aksi === 'validated' ? 'Setujui' : 'Kirim Revisi')"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function validasiManager() {
    return {
        detailModal: false, validasiModal: false, detailData: null,
        validasiId: null, validasiGuruNama: '', aksi: '', catatanValidasi: '',
        validasiLoading: false, validasiError: '',
        init() { this.$nextTick(() => lucide.createIcons()); },

        async viewDetail(id) {
            const res = await fetch(`/ks/validasi/${id}`, { headers: { 'Accept': 'application/json' } });
            this.detailData = await res.json();
            this.detailModal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        openValidasi(id, nama) {
            this.validasiId = id; this.validasiGuruNama = nama;
            this.aksi = ''; this.catatanValidasi = ''; this.validasiError = '';
            this.validasiModal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        async kirimValidasi() {
            if (!this.aksi) return;
            if (this.aksi === 'revisi' && !this.catatanValidasi.trim()) {
                this.validasiError = 'Catatan wajib diisi saat meminta revisi.'; return;
            }
            this.validasiLoading = true; this.validasiError = '';
            try {
                const res = await fetch(`/ks/validasi/${this.validasiId}`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ aksi: this.aksi, catatan_validasi: this.catatanValidasi })
                });
                const data = await res.json();
                if (!res.ok) { this.validasiError = data.message || 'Gagal.'; }
                else {
                    this.validasiModal = false;
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 1800, showConfirmButton: false, toast: true, position: 'top-end' });
                    setTimeout(() => location.reload(), 1500);
                }
            } catch { this.validasiError = 'Gagal terhubung.'; }
            finally { this.validasiLoading = false; }
        }
    }
}
</script>
@endpush
