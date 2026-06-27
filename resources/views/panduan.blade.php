@extends('layouts.app')

@section('title', 'Panduan Penggunaan')
@section('page-title', 'Panduan Penggunaan Sistem')

@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Beranda</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <span class="text-slate-700 dark:text-zinc-200 font-medium">Panduan Penggunaan</span>
@endsection

@section('content')
<div x-data="{ tab: 0 }">

@auth

{{-- ================================================================== --}}
{{-- ADMIN --}}
{{-- ================================================================== --}}
@if(auth()->user()->hasRole('admin'))

<div class="flex flex-col lg:flex-row gap-6">

    {{-- Sidebar navigasi tab --}}
    <aside class="lg:w-56 flex-shrink-0">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200/80 dark:border-zinc-700/50 overflow-hidden sticky top-24">
            <div class="px-4 py-3 border-b border-slate-200/80 dark:border-zinc-700/50">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center">
                        <i data-lucide="shield" class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-700 dark:text-zinc-300 uppercase tracking-wider">Admin</p>
                </div>
            </div>
            <nav class="p-2 space-y-0.5">
                @php
                $adminNav = [
                    ['icon'=>'rocket','label'=>'Wajib Setup Awal'],
                    ['icon'=>'calendar-clock','label'=>'Kelola Jadwal'],
                    ['icon'=>'notebook-text','label'=>'Monitor Jurnal'],
                    ['icon'=>'database','label'=>'Master Data'],
                ];
                @endphp
                @foreach($adminNav as $i => $nav)
                <button @click="tab = {{ $i }}" type="button"
                    :class="tab === {{ $i }} ? 'bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400' : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800 hover:text-slate-800 dark:hover:text-zinc-100'"
                    class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors text-left">
                    <i data-lucide="{{ $nav['icon'] }}" class="w-4 h-4 flex-shrink-0"></i>
                    <span>{{ $nav['label'] }}</span>
                </button>
                @endforeach
            </nav>
        </div>
    </aside>

    {{-- Konten --}}
    <div class="flex-1 min-w-0 space-y-4">

        {{-- ---- Tab 0: Wajib Setup Awal ---- --}}
        <div x-show="tab === 0" x-transition.opacity>

            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200/80 dark:border-zinc-700/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200/80 dark:border-zinc-700/50 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center">
                        <i data-lucide="rocket" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800 dark:text-white">Wajib Setup Awal</h2>
                        <p class="text-xs text-slate-400 dark:text-zinc-500">Selesaikan langkah-langkah ini sebelum guru dapat mengisi jurnal</p>
                    </div>
                </div>

                <div class="p-6">
                    <div class="mb-6 flex items-start gap-3 p-4 bg-amber-50 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-800/30 rounded-xl text-sm text-amber-800 dark:text-amber-300">
                        <i data-lucide="triangle-alert" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
                        <p><strong>Penting!</strong> Sebelum guru dapat mengisi jurnal, Admin <strong>wajib</strong> menyelesaikan 5 langkah setup berikut secara berurutan. Lewati satu langkah pun, sistem tidak akan berfungsi dengan benar.</p>
                    </div>

                    <div class="space-y-0">

                        {{-- Step 1 --}}
                        <div class="flex gap-5">
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full bg-amber-500 text-white flex items-center justify-center text-sm font-bold flex-shrink-0 shadow-sm">1</div>
                                <div class="w-0.5 flex-1 bg-slate-200 dark:bg-zinc-700 mt-2"></div>
                            </div>
                            <div class="pb-8 flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <i data-lucide="calendar-range" class="w-5 h-5 text-amber-500"></i>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">Buat & Aktifkan Tahun Ajaran</h3>
                                </div>
                                <p class="text-sm text-slate-500 dark:text-zinc-400 mb-3">Tahun ajaran adalah fondasi seluruh data. Semua jadwal dan jurnal terikat ke tahun ajaran yang aktif. Hanya boleh ada <strong>satu</strong> tahun ajaran aktif dalam satu waktu.</p>
                                <div class="bg-slate-50 dark:bg-zinc-800 rounded-xl p-4 text-sm text-slate-600 dark:text-zinc-400 space-y-2 border border-slate-200/80 dark:border-zinc-700/50">
                                    <div class="flex items-start gap-2">
                                        <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-zinc-700 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">a</span>
                                        <p>Buka <strong>Master Data → Tahun Ajaran</strong> di sidebar kiri</p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-zinc-700 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">b</span>
                                        <p>Klik <strong>"Tambah Tahun Ajaran"</strong>, isi nama (contoh: <em>2024/2025</em>) dan pilih semester</p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-zinc-700 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">c</span>
                                        <p>Klik tombol <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-400 rounded-lg text-xs font-semibold">Aktivasi</span> pada baris tahun ajaran yang baru dibuat</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 2 --}}
                        <div class="flex gap-5">
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full bg-amber-500 text-white flex items-center justify-center text-sm font-bold flex-shrink-0 shadow-sm">2</div>
                                <div class="w-0.5 flex-1 bg-slate-200 dark:bg-zinc-700 mt-2"></div>
                            </div>
                            <div class="pb-8 flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <i data-lucide="users" class="w-5 h-5 text-amber-500"></i>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">Tambah Pengguna Guru</h3>
                                </div>
                                <p class="text-sm text-slate-500 dark:text-zinc-400 mb-3">Daftarkan semua guru yang akan menggunakan sistem, lalu bagikan kredensial login kepada mereka agar bisa masuk ke aplikasi.</p>
                                <div class="bg-slate-50 dark:bg-zinc-800 rounded-xl p-4 text-sm text-slate-600 dark:text-zinc-400 space-y-2 border border-slate-200/80 dark:border-zinc-700/50">
                                    <div class="flex items-start gap-2">
                                        <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-zinc-700 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">a</span>
                                        <p>Buka <strong>Master Data → Pengguna</strong></p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-zinc-700 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">b</span>
                                        <p>Klik <strong>"Tambah Pengguna"</strong> untuk input satu per satu, <em>atau</em> gunakan <strong>"Import Excel"</strong> untuk banyak guru sekaligus (unduh template terlebih dahulu)</p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-zinc-700 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">c</span>
                                        <p>Set role sebagai <strong>Guru</strong> dan pastikan username (kode guru) unik</p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-zinc-700 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">d</span>
                                        <p>Bagikan <strong>Username</strong> (kode guru) dan password default <code class="px-1.5 py-0.5 bg-slate-200 dark:bg-zinc-700 rounded font-mono text-xs">12345678</code> kepada setiap guru</p>
                                    </div>
                                </div>
                                <div class="mt-3 flex items-start gap-2 p-3 bg-blue-50 dark:bg-blue-950/30 border border-blue-200/60 dark:border-blue-800/30 rounded-xl text-sm text-blue-700 dark:text-blue-400">
                                    <i data-lucide="info" class="w-4 h-4 flex-shrink-0 mt-0.5"></i>
                                    <p>Ingatkan guru untuk segera mengingat username mereka. Password default dapat diganti setelah login pertama.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        <div class="flex gap-5">
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full bg-amber-500 text-white flex items-center justify-center text-sm font-bold flex-shrink-0 shadow-sm">3</div>
                                <div class="w-0.5 flex-1 bg-slate-200 dark:bg-zinc-700 mt-2"></div>
                            </div>
                            <div class="pb-8 flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <i data-lucide="book-marked" class="w-5 h-5 text-amber-500"></i>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">Tambah Mata Pelajaran</h3>
                                </div>
                                <p class="text-sm text-slate-500 dark:text-zinc-400 mb-3">Daftarkan semua mata pelajaran yang diajarkan di sekolah. Data ini akan digunakan saat membuat jadwal dan mengisi jurnal.</p>
                                <div class="bg-slate-50 dark:bg-zinc-800 rounded-xl p-4 text-sm text-slate-600 dark:text-zinc-400 space-y-2 border border-slate-200/80 dark:border-zinc-700/50">
                                    <div class="flex items-start gap-2">
                                        <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-zinc-700 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">a</span>
                                        <p>Buka <strong>Master Data → Mata Pelajaran</strong></p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-zinc-700 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">b</span>
                                        <p>Klik <strong>"Tambah Mapel"</strong>, isi nama mata pelajaran</p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-zinc-700 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">c</span>
                                        <p>Ulangi untuk semua mata pelajaran yang ada</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 4 --}}
                        <div class="flex gap-5">
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full bg-amber-500 text-white flex items-center justify-center text-sm font-bold flex-shrink-0 shadow-sm">4</div>
                                <div class="w-0.5 flex-1 bg-slate-200 dark:bg-zinc-700 mt-2"></div>
                            </div>
                            <div class="pb-8 flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <i data-lucide="school" class="w-5 h-5 text-amber-500"></i>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">Tambah Kelas</h3>
                                </div>
                                <p class="text-sm text-slate-500 dark:text-zinc-400 mb-3">Daftarkan semua rombongan belajar (kelas) yang ada di sekolah. Data kelas diperlukan untuk menyusun jadwal.</p>
                                <div class="bg-slate-50 dark:bg-zinc-800 rounded-xl p-4 text-sm text-slate-600 dark:text-zinc-400 space-y-2 border border-slate-200/80 dark:border-zinc-700/50">
                                    <div class="flex items-start gap-2">
                                        <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-zinc-700 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">a</span>
                                        <p>Buka <strong>Master Data → Kelas</strong></p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-zinc-700 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">b</span>
                                        <p>Klik <strong>"Tambah Kelas"</strong>, isi nama kelas (contoh: <em>X-A, XI-IPA, XII-TKJ-1</em>)</p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-zinc-700 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">c</span>
                                        <p>Ulangi untuk semua kelas yang ada</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 5 --}}
                        <div class="flex gap-5">
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full bg-amber-500 text-white flex items-center justify-center text-sm font-bold flex-shrink-0 shadow-sm">5</div>
                                <div class="w-0.5 flex-1 bg-slate-200 dark:bg-zinc-700 mt-2"></div>
                            </div>
                            <div class="pb-8 flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <i data-lucide="calendar-clock" class="w-5 h-5 text-amber-500"></i>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">Buat Jadwal Mengajar</h3>
                                </div>
                                <p class="text-sm text-slate-500 dark:text-zinc-400 mb-3">Input jadwal mengajar untuk setiap guru. Jadwal bisa dikelola per kelas atau per guru sesuai kebutuhan. Jadwal ini yang akan menjadi acuan pengisian jurnal oleh guru.</p>
                                <div class="bg-slate-50 dark:bg-zinc-800 rounded-xl p-4 text-sm text-slate-600 dark:text-zinc-400 space-y-2 border border-slate-200/80 dark:border-zinc-700/50">
                                    <div class="flex items-start gap-2">
                                        <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-zinc-700 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">a</span>
                                        <p>Buka menu <strong>Jadwal Kelas</strong> di sidebar</p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-zinc-700 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">b</span>
                                        <p>Pilih tampilan <strong>"Per Kelas"</strong> atau <strong>"Per Guru"</strong>, kemudian pilih kelas/guru yang ingin dikelola</p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-zinc-700 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">c</span>
                                        <p>Klik <strong>"Tambah Jadwal"</strong>, isi guru, mapel, hari, jam mulai, dan jam selesai — lalu simpan</p>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-zinc-700 flex items-center justify-center text-[10px] font-bold flex-shrink-0 mt-0.5">d</span>
                                        <p>Ulangi untuk seluruh sesi mengajar semua guru</p>
                                    </div>
                                </div>
                                <div class="mt-3 flex items-start gap-2 p-3 bg-green-50 dark:bg-green-950/30 border border-green-200/60 dark:border-green-800/30 rounded-xl text-sm text-green-700 dark:text-green-400">
                                    <i data-lucide="lightbulb" class="w-4 h-4 flex-shrink-0 mt-0.5"></i>
                                    <p><strong>Tips:</strong> Untuk tahun ajaran baru, gunakan fitur <strong>Clone Jadwal</strong> di menu <strong>Master Data → Tahun Ajaran</strong> untuk menyalin semua jadwal dari tahun sebelumnya secara otomatis — menghemat waktu input!</p>
                                </div>
                            </div>
                        </div>

                        {{-- Done --}}
                        <div class="flex gap-5">
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <i data-lucide="check" class="w-5 h-5"></i>
                                </div>
                            </div>
                            <div class="flex-1 pb-2">
                                <h3 class="text-sm font-bold text-green-600 dark:text-green-400 mb-1">Sistem Siap Digunakan!</h3>
                                <p class="text-sm text-slate-500 dark:text-zinc-400">Guru sudah dapat login menggunakan username dan password yang Anda bagikan, lalu mulai mengisi jurnal mengajar sesuai jadwal yang telah dibuat.</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- ---- Tab 1: Kelola Jadwal ---- --}}
        <div x-show="tab === 1" x-transition.opacity class="space-y-4">

            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200/80 dark:border-zinc-700/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200/80 dark:border-zinc-700/50 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center">
                        <i data-lucide="calendar-clock" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800 dark:text-white">Kelola Jadwal</h2>
                        <p class="text-xs text-slate-400 dark:text-zinc-500">Atur jadwal mengajar seluruh guru</p>
                    </div>
                </div>
                <div class="p-6 space-y-5">

                    <div>
                        <h3 class="text-sm font-bold text-slate-700 dark:text-zinc-300 mb-3 flex items-center gap-2">
                            <i data-lucide="layout-grid" class="w-4 h-4 text-amber-500"></i>
                            Dua Cara Melihat Jadwal
                        </h3>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="p-4 bg-amber-50 dark:bg-amber-950/20 rounded-xl border border-amber-200/60 dark:border-amber-800/30">
                                <p class="text-sm font-semibold text-amber-700 dark:text-amber-400 mb-2 flex items-center gap-1.5">
                                    <i data-lucide="school" class="w-4 h-4"></i> Per Kelas
                                </p>
                                <p class="text-sm text-slate-500 dark:text-zinc-400">Menampilkan semua guru yang mengajar di satu kelas tertentu, dikelompokkan per hari. Cocok untuk memastikan tidak ada jam bentrok dalam satu kelas.</p>
                            </div>
                            <div class="p-4 bg-blue-50 dark:bg-blue-950/20 rounded-xl border border-blue-200/60 dark:border-blue-800/30">
                                <p class="text-sm font-semibold text-blue-700 dark:text-blue-400 mb-2 flex items-center gap-1.5">
                                    <i data-lucide="user" class="w-4 h-4"></i> Per Guru
                                </p>
                                <p class="text-sm text-slate-500 dark:text-zinc-400">Menampilkan semua kelas yang diajar satu guru tertentu, dikelompokkan per hari. Cocok untuk memastikan beban guru tidak bentrok antar jadwal.</p>
                            </div>
                        </div>
                        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-3">Kedua tampilan mengelola data yang sama — hanya perspektifnya yang berbeda. Perubahan di satu tampilan langsung terlihat di tampilan lainnya.</p>
                    </div>

                    <hr class="border-slate-200/80 dark:border-zinc-700/50">

                    <div>
                        <h3 class="text-sm font-bold text-slate-700 dark:text-zinc-300 mb-3 flex items-center gap-2">
                            <i data-lucide="plus-circle" class="w-4 h-4 text-amber-500"></i>
                            Menambah & Mengedit Jadwal
                        </h3>
                        <div class="bg-slate-50 dark:bg-zinc-800 rounded-xl p-4 text-sm text-slate-600 dark:text-zinc-400 space-y-2 border border-slate-200/80 dark:border-zinc-700/50">
                            <p>1. Pilih kelas atau guru dari dropdown di halaman Jadwal Kelas</p>
                            <p>2. Klik <strong>"Tambah Jadwal"</strong> — form input akan muncul</p>
                            <p>3. Isi: <strong>Guru, Mata Pelajaran, Hari, Jam Mulai, Jam Selesai</strong></p>
                            <p>4. Klik Simpan. Jadwal langsung tampil di grid</p>
                            <p>5. Untuk edit atau hapus: klik ikon pensil/tempat sampah pada baris jadwal</p>
                        </div>
                    </div>

                    <hr class="border-slate-200/80 dark:border-zinc-700/50">

                    <div>
                        <h3 class="text-sm font-bold text-slate-700 dark:text-zinc-300 mb-3 flex items-center gap-2">
                            <i data-lucide="printer" class="w-4 h-4 text-amber-500"></i>
                            Cetak Jadwal
                        </h3>
                        <div class="grid sm:grid-cols-3 gap-3">
                            @php $cetaks = [['Cetak Semua','Cetak jadwal seluruh kelas dalam satu dokumen sekaligus'],['Cetak per Kelas','Cetak jadwal satu kelas tertentu saja'],['Cetak per Guru','Cetak jadwal satu guru tertentu saja']]; @endphp
                            @foreach($cetaks as $c)
                            <div class="p-3.5 bg-slate-50 dark:bg-zinc-800 rounded-xl border border-slate-200/80 dark:border-zinc-700/50">
                                <p class="text-xs font-semibold text-slate-700 dark:text-zinc-300 mb-1">{{ $c[0] }}</p>
                                <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $c[1] }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <hr class="border-slate-200/80 dark:border-zinc-700/50">

                    <div>
                        <h3 class="text-sm font-bold text-slate-700 dark:text-zinc-300 mb-3 flex items-center gap-2">
                            <i data-lucide="copy" class="w-4 h-4 text-amber-500"></i>
                            Clone Jadwal (Tahun Ajaran Baru)
                        </h3>
                        <div class="flex items-start gap-3 p-4 bg-green-50 dark:bg-green-950/30 border border-green-200/60 dark:border-green-800/30 rounded-xl text-sm text-green-800 dark:text-green-300">
                            <i data-lucide="lightbulb" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
                            <div class="space-y-1">
                                <p>Saat membuat tahun ajaran baru, gunakan fitur <strong>Clone Jadwal</strong> di <strong>Master Data → Tahun Ajaran</strong>.</p>
                                <p>Fitur ini menyalin seluruh jadwal dari tahun ajaran sebelumnya ke tahun ajaran baru secara otomatis — tidak perlu input ulang satu per satu.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ---- Tab 2: Monitor Jurnal ---- --}}
        <div x-show="tab === 2" x-transition.opacity class="space-y-4">

            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200/80 dark:border-zinc-700/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200/80 dark:border-zinc-700/50 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center">
                        <i data-lucide="notebook-text" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800 dark:text-white">Monitor Jurnal</h2>
                        <p class="text-xs text-slate-400 dark:text-zinc-500">Pantau aktivitas pengisian jurnal seluruh guru</p>
                    </div>
                </div>
                <div class="p-6 space-y-5">

                    <div>
                        <h3 class="text-sm font-bold text-slate-700 dark:text-zinc-300 mb-3 flex items-center gap-2">
                            <i data-lucide="layout-dashboard" class="w-4 h-4 text-amber-500"></i>
                            Dashboard Admin
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-zinc-400 mb-3">Dashboard menampilkan kondisi pengisian jurnal secara real-time:</p>
                        <div class="space-y-2">
                            @php
                            $dashItems = [
                                ['Mengajar Sekarang','Menampilkan daftar guru yang sedang dalam jam mengajar saat ini berdasarkan jadwal aktif'],
                                ['Jurnal Hari Ini','Jumlah jurnal yang sudah diisi guru hari ini'],
                                ['Guru Belum Isi','Daftar nama guru yang memiliki jadwal hari ini namun belum mengisi jurnal sama sekali'],
                                ['Jurnal Terbaru','Daftar jurnal terbaru dari seluruh guru'],
                            ];
                            @endphp
                            @foreach($dashItems as $d)
                            <div class="flex gap-3 p-3.5 rounded-xl border border-slate-200 dark:border-zinc-700">
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-green-500 flex-shrink-0 mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-zinc-300">{{ $d[0] }}</p>
                                    <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">{{ $d[1] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <hr class="border-slate-200/80 dark:border-zinc-700/50">

                    <div>
                        <h3 class="text-sm font-bold text-slate-700 dark:text-zinc-300 mb-3 flex items-center gap-2">
                            <i data-lucide="search" class="w-4 h-4 text-amber-500"></i>
                            Lihat Detail Jurnal Guru
                        </h3>
                        <div class="bg-slate-50 dark:bg-zinc-800 rounded-xl p-4 text-sm text-slate-600 dark:text-zinc-400 space-y-2 border border-slate-200/80 dark:border-zinc-700/50">
                            <p>1. Buka menu <strong>Jurnal Guru</strong> di sidebar</p>
                            <p>2. Gunakan filter untuk menyaring jurnal: <strong>Guru, Kelas, Rentang Tanggal</strong></p>
                            <p>3. Klik pada baris jurnal untuk melihat detail lengkap</p>
                            <p>4. Detail mencakup: materi, catatan tambahan, status keterlambatan, dan foto lampiran</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- ---- Tab 3: Master Data ---- --}}
        <div x-show="tab === 3" x-transition.opacity class="space-y-4">

            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200/80 dark:border-zinc-700/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200/80 dark:border-zinc-700/50 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center">
                        <i data-lucide="database" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800 dark:text-white">Master Data</h2>
                        <p class="text-xs text-slate-400 dark:text-zinc-500">Data referensi yang digunakan seluruh sistem</p>
                    </div>
                </div>
                <div class="p-6">
                    <div class="mb-5 flex items-start gap-3 p-4 bg-amber-50 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-800/30 rounded-xl text-sm text-amber-800 dark:text-amber-300">
                        <i data-lucide="triangle-alert" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
                        <p>Master data adalah fondasi sistem. Kelola dengan hati-hati — perubahan akan berdampak ke seluruh jadwal dan jurnal yang sudah ada.</p>
                    </div>
                    <div class="space-y-3">
                        @php
                        $masterItems = [
                            ['users','Pengguna','Kelola akun admin, guru, dan kepala sekolah. Untuk guru yang sudah tidak aktif, gunakan fitur Nonaktifkan (jangan hapus) agar data jurnal mereka tetap tersimpan.','slate'],
                            ['school','Kelas','Daftar rombongan belajar. Nama kelas yang diedit akan otomatis terupdate di semua jadwal dan jurnal yang terkait.','blue'],
                            ['book-marked','Mata Pelajaran','Daftar mata pelajaran yang diajarkan. Pastikan nama mapel konsisten agar laporan dan filter mudah digunakan.','purple'],
                            ['calendar-range','Tahun Ajaran','Hanya boleh ada SATU tahun ajaran aktif. Untuk ganti semester atau tahun baru: Tambah tahun ajaran baru → Aktifkan → Clone Jadwal (opsional).','amber'],
                            ['building-2','Identitas Sekolah','Nama sekolah, NPSN, dan alamat. Informasi ini muncul di header ketika mencetak jadwal dan laporan.','green'],
                        ];
                        @endphp
                        @foreach($masterItems as $m)
                        <div class="flex gap-4 p-4 rounded-xl border border-slate-200 dark:border-zinc-700">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-zinc-800 flex items-center justify-center text-slate-500 dark:text-zinc-400 flex-shrink-0">
                                <i data-lucide="{{ $m[0] }}" class="w-5 h-5"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-slate-700 dark:text-zinc-300 mb-1">{{ $m[1] }}</p>
                                <p class="text-sm text-slate-500 dark:text-zinc-400">{{ $m[2] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- end flex --}}
</div>


{{-- ================================================================== --}}
{{-- GURU --}}
{{-- ================================================================== --}}
@elseif(auth()->user()->hasRole('guru'))

<div class="flex flex-col lg:flex-row gap-6">

    <aside class="lg:w-56 flex-shrink-0">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200/80 dark:border-zinc-700/50 overflow-hidden sticky top-24">
            <div class="px-4 py-3 border-b border-slate-200/80 dark:border-zinc-700/50">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center">
                        <i data-lucide="user" class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-700 dark:text-zinc-300 uppercase tracking-wider">Guru</p>
                </div>
            </div>
            <nav class="p-2 space-y-0.5">
                @php $guruNav = [['pencil-line','Cara Isi Jurnal'],['pencil','Edit & Hapus Jurnal'],['lightbulb','Tips']]; @endphp
                @foreach($guruNav as $i => $nav)
                <button @click="tab = {{ $i }}" type="button"
                    :class="tab === {{ $i }} ? 'bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400' : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800 hover:text-slate-800 dark:hover:text-zinc-100'"
                    class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors text-left">
                    <i data-lucide="{{ $nav[0] }}" class="w-4 h-4 flex-shrink-0"></i>
                    <span>{{ $nav[1] }}</span>
                </button>
                @endforeach
            </nav>
        </div>
    </aside>

    <div class="flex-1 min-w-0">

        {{-- Tab 0: Cara Isi Jurnal --}}
        <div x-show="tab === 0" x-transition.opacity>
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200/80 dark:border-zinc-700/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200/80 dark:border-zinc-700/50 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center">
                        <i data-lucide="pencil-line" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800 dark:text-white">Cara Mengisi Jurnal</h2>
                        <p class="text-xs text-slate-400 dark:text-zinc-500">Panduan langkah demi langkah pengisian jurnal mengajar</p>
                    </div>
                </div>
                <div class="p-6">
                    <div class="mb-6 flex items-start gap-3 p-4 bg-blue-50 dark:bg-blue-950/30 border border-blue-200/60 dark:border-blue-800/30 rounded-xl text-sm text-blue-800 dark:text-blue-300">
                        <i data-lucide="info" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
                        <p>Isi jurnal <strong>setiap kali selesai mengajar</strong>. Jurnal yang belum diisi akan tercatat sebagai tidak hadir mengisi.</p>
                    </div>
                    <div class="space-y-0">
                        @php
                        $steps = [
                            ['n'=>1,'icon'=>'layout-dashboard','title'=>'Cek Dashboard','body'=>'Setelah login, lihat bagian <strong>Jadwal Hari Ini</strong> di dashboard. Di sana terlihat semua sesi mengajar Anda hari ini beserta statusnya — sudah diisi atau belum.'],
                            ['n'=>2,'icon'=>'clipboard-pen','title'=>'Klik Tombol "Isi"','body'=>'Klik tombol <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 rounded-lg text-xs font-semibold">Isi</span> pada jadwal yang ingin diisi. Form jurnal akan terbuka otomatis dengan kelas dan mata pelajaran sudah terisi.'],
                            ['n'=>3,'icon'=>'edit','title'=>'Lengkapi Form Jurnal','body'=>'<ul class="space-y-1 list-none"><li>• <strong>Jam Masuk Aktual</strong> (wajib) — isi jam Anda benar-benar masuk kelas</li><li>• <strong>Materi Pembelajaran</strong> (wajib) — topik/materi yang diajarkan hari ini</li><li>• <strong>Catatan Tambahan</strong> — nama siswa yang tidak hadir, kendala, atau informasi lainnya</li></ul>'],
                            ['n'=>4,'icon'=>'image','title'=>'Upload Foto (Opsional)','body'=>'Lampirkan foto bukti mengajar seperti foto papan tulis, aktivitas siswa, atau hasil karya. Maksimal <strong>5 foto</strong>, format JPG/PNG/WEBP, ukuran masing-masing maksimal <strong>5 MB</strong>.'],
                            ['n'=>5,'icon'=>'save','title'=>'Simpan Jurnal','body'=>'Klik <strong>"Simpan Jurnal"</strong> — jurnal langsung tersimpan dan bisa dilihat oleh Kepala Sekolah. Anda tetap bisa mengedit atau menghapus jurnal kapan saja.'],
                        ];
                        @endphp
                        @foreach($steps as $idx => $step)
                        <div class="flex gap-5 {{ $idx < count($steps)-1 ? '' : '' }}">
                            <div class="flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full bg-amber-500 text-white flex items-center justify-center text-sm font-bold flex-shrink-0 shadow-sm">{{ $step['n'] }}</div>
                                @if($idx < count($steps)-1)
                                <div class="w-0.5 flex-1 bg-slate-200 dark:bg-zinc-700 mt-2"></div>
                                @endif
                            </div>
                            <div class="pb-8 flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <i data-lucide="{{ $step['icon'] }}" class="w-5 h-5 text-amber-500"></i>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-white">{{ $step['title'] }}</h3>
                                </div>
                                <p class="text-sm text-slate-500 dark:text-zinc-400">{!! $step['body'] !!}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab 1: Edit & Hapus --}}
        <div x-show="tab === 1" x-transition.opacity class="space-y-4">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200/80 dark:border-zinc-700/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200/80 dark:border-zinc-700/50 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center">
                        <i data-lucide="pencil" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800 dark:text-white">Edit & Hapus Jurnal</h2>
                        <p class="text-xs text-slate-400 dark:text-zinc-500">Jurnal bisa diedit atau dihapus kapan saja</p>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex gap-4 p-4 rounded-xl border border-green-200/60 dark:border-green-800/30 bg-green-50 dark:bg-green-950/30">
                        <i data-lucide="pencil" class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-bold text-green-700 dark:text-green-300 mb-1">Edit Jurnal</p>
                            <p class="text-sm text-slate-500 dark:text-zinc-400">Klik tombol <strong>Edit</strong> pada daftar jurnal untuk mengubah materi, jam, foto, atau catatan tambahan. Perubahan langsung tersimpan.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 p-4 rounded-xl border border-red-200/60 dark:border-red-800/30 bg-red-50 dark:bg-red-950/30">
                        <i data-lucide="trash-2" class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-bold text-red-700 dark:text-red-300 mb-1">Hapus Jurnal</p>
                            <p class="text-sm text-slate-500 dark:text-zinc-400">Klik tombol <strong>Hapus</strong> untuk menghapus jurnal yang tidak diperlukan. Tindakan ini tidak dapat dibatalkan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab 2: Tips --}}
        <div x-show="tab === 2" x-transition.opacity>
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200/80 dark:border-zinc-700/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200/80 dark:border-zinc-700/50 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center">
                        <i data-lucide="lightbulb" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800 dark:text-white">Tips Menggunakan Sistem</h2>
                        <p class="text-xs text-slate-400 dark:text-zinc-500">Cara memaksimalkan penggunaan aplikasi jurnal</p>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid sm:grid-cols-2 gap-4">
                        @php
                        $tips = [
                            ['zap','Isi Sesegera Mungkin','Isi jurnal segera setelah selesai mengajar agar detail materi masih segar di ingatan. Jurnal untuk hari-hari sebelumnya yang terlewat tetap bisa diisi.'],
                            ['camera','Foto Adalah Bukti','Lampirkan foto papan tulis atau aktivitas belajar sebagai bukti mengajar yang konkret. Ini membantu KS dalam meninjau jurnal Anda.'],
                            ['alarm-clock','Perhatikan Jam Masuk','Isi jam masuk aktual secara jujur. Keterlambatan dihitung otomatis dari selisih jam masuk Anda dengan jam mulai di jadwal.'],
                            ['pencil','Bisa Diedit Kapan Saja','Jurnal yang sudah tersimpan tetap bisa diedit atau dihapus kapan saja. Tidak ada proses submit — langsung tersimpan.'],
                            ['smartphone','Bisa via HP','Aplikasi ini responsif dan dapat diakses dari smartphone kapan saja dan di mana saja — tidak harus dari komputer.'],
                        ];
                        @endphp
                        @foreach($tips as $tip)
                        <div class="flex gap-3 p-4 rounded-xl border border-slate-200 dark:border-zinc-700">
                            <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950/40 flex items-center justify-center text-amber-500 flex-shrink-0">
                                <i data-lucide="{{ $tip[0] }}" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-700 dark:text-zinc-300 mb-1">{{ $tip[1] }}</p>
                                <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $tip[2] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


{{-- ================================================================== --}}
{{-- KS (Kepala Sekolah) --}}
{{-- ================================================================== --}}
@elseif(auth()->user()->hasRole('ks'))

<div class="flex flex-col lg:flex-row gap-6">

    <aside class="lg:w-56 flex-shrink-0">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200/80 dark:border-zinc-700/50 overflow-hidden sticky top-24">
            <div class="px-4 py-3 border-b border-slate-200/80 dark:border-zinc-700/50">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center">
                        <i data-lucide="star" class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-700 dark:text-zinc-300 uppercase tracking-wider">Kepala Sekolah</p>
                </div>
            </div>
            <nav class="p-2 space-y-0.5">
                @php $ksNav = [['bar-chart-2','Dashboard KS'],['notebook-text','Lihat Jurnal Guru'],['lightbulb','Tips']]; @endphp
                @foreach($ksNav as $i => $nav)
                <button @click="tab = {{ $i }}" type="button"
                    :class="tab === {{ $i }} ? 'bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400' : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800 hover:text-slate-800 dark:hover:text-zinc-100'"
                    class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors text-left">
                    <i data-lucide="{{ $nav[0] }}" class="w-4 h-4 flex-shrink-0"></i>
                    <span>{{ $nav[1] }}</span>
                </button>
                @endforeach
            </nav>
        </div>
    </aside>

    <div class="flex-1 min-w-0">

        {{-- Tab 0: Dashboard KS --}}
        <div x-show="tab === 0" x-transition.opacity>
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200/80 dark:border-zinc-700/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200/80 dark:border-zinc-700/50 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center">
                        <i data-lucide="bar-chart-2" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800 dark:text-white">Dashboard Kepala Sekolah</h2>
                        <p class="text-xs text-slate-400 dark:text-zinc-500">Ringkasan kondisi pengisian jurnal guru secara real-time</p>
                    </div>
                </div>
                <div class="p-6 space-y-5">
                    <div class="space-y-3">
                        @php
                        $ksCards = [
                            ['users','Total Guru Aktif','Jumlah guru yang terdaftar dan aktif menggunakan sistem.'],
                            ['alert-triangle','Guru Belum Isi Hari Ini','Jumlah guru yang memiliki jadwal hari ini tetapi belum mengisi jurnal. Angka ini diperbarui secara otomatis.'],
                            ['notebook-pen','Jurnal Hari Ini','Jumlah jurnal yang sudah diisi seluruh guru hari ini.'],
                            ['calendar-check','Jurnal Bulan Ini','Total jurnal yang sudah diisi seluruh guru pada bulan berjalan.'],
                        ];
                        @endphp
                        @foreach($ksCards as $c)
                        <div class="flex gap-4 p-4 rounded-xl border border-slate-200 dark:border-zinc-700">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-zinc-800 flex items-center justify-center text-amber-500 flex-shrink-0">
                                <i data-lucide="{{ $c[0] }}" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-700 dark:text-zinc-300 mb-0.5">{{ $c[1] }}</p>
                                <p class="text-sm text-slate-500 dark:text-zinc-400">{{ $c[2] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <hr class="border-slate-200/80 dark:border-zinc-700/50">

                    <div>
                        <h3 class="text-sm font-bold text-slate-700 dark:text-zinc-300 mb-3 flex items-center gap-2">
                            <i data-lucide="history" class="w-4 h-4 text-amber-500"></i>
                            Jurnal Terbaru
                        </h3>
                        <div class="bg-slate-50 dark:bg-zinc-800 rounded-xl p-4 text-sm text-slate-600 dark:text-zinc-400 space-y-2 border border-slate-200/80 dark:border-zinc-700/50">
                            <p>• <strong>Jurnal Terbaru</strong> — daftar jurnal terbaru dari seluruh guru yang bisa langsung dicek</p>
                            <p>• <strong>Daftar Guru Belum Isi Hari Ini</strong> — nama guru yang jadwalnya ada hari ini namun belum mengisi jurnal</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab 1: Lihat Jurnal Guru --}}
        <div x-show="tab === 1" x-transition.opacity>
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200/80 dark:border-zinc-700/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200/80 dark:border-zinc-700/50 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center">
                        <i data-lucide="notebook-text" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800 dark:text-white">Melihat Jurnal Guru</h2>
                        <p class="text-xs text-slate-400 dark:text-zinc-500">Cara mengakses dan membaca jurnal yang telah diisi guru</p>
                    </div>
                </div>
                <div class="p-6 space-y-5">

                    <div class="bg-slate-50 dark:bg-zinc-800 rounded-xl p-4 text-sm text-slate-600 dark:text-zinc-400 space-y-2 border border-slate-200/80 dark:border-zinc-700/50">
                        <p>1. Buka menu <strong>Jurnal Guru</strong> di sidebar kiri</p>
                        <p>2. Gunakan filter yang tersedia untuk menyaring jurnal yang ingin dilihat</p>
                        <p>3. Klik pada baris jurnal untuk membuka detail lengkap</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-slate-700 dark:text-zinc-300 mb-3 flex items-center gap-2">
                            <i data-lucide="filter" class="w-4 h-4 text-amber-500"></i>
                            Filter yang Tersedia
                        </h3>
                        <div class="grid sm:grid-cols-2 gap-3">
                            @php $filters = [['user','Nama Guru','Tampilkan jurnal milik satu guru tertentu saja'],['school','Kelas','Filter berdasarkan kelas yang diajar'],['calendar','Rentang Tanggal','Lihat jurnal dalam periode waktu tertentu']]; @endphp
                            @foreach($filters as $f)
                            <div class="flex gap-3 p-3.5 rounded-xl border border-slate-200 dark:border-zinc-700">
                                <i data-lucide="{{ $f[0] }}" class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-zinc-300">{{ $f[1] }}</p>
                                    <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">{{ $f[2] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-bold text-slate-700 dark:text-zinc-300 mb-3 flex items-center gap-2">
                            <i data-lucide="eye" class="w-4 h-4 text-amber-500"></i>
                            Informasi dalam Detail Jurnal
                        </h3>
                        <div class="bg-slate-50 dark:bg-zinc-800 rounded-xl p-4 text-sm text-slate-600 dark:text-zinc-400 space-y-1.5 border border-slate-200/80 dark:border-zinc-700/50">
                            <p>• Materi yang disampaikan guru saat mengajar</p>
                            <p>• Informasi keterlambatan masuk kelas (jika ada)</p>
                            <p>• Foto-foto lampiran bukti mengajar</p>
                            <p>• Catatan tambahan dari guru (misal: siswa yang tidak hadir)</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Tab 2: Tips KS --}}
        <div x-show="tab === 2" x-transition.opacity>
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-slate-200/80 dark:border-zinc-700/50 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200/80 dark:border-zinc-700/50 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center">
                        <i data-lucide="lightbulb" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800 dark:text-white">Tips Pemantauan</h2>
                        <p class="text-xs text-slate-400 dark:text-zinc-500">Cara efektif menggunakan data sistem untuk pembinaan guru</p>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid sm:grid-cols-2 gap-4">
                        @php
                        $ksTips = [
                            ['calendar','Pantau Setiap Hari','Cek dashboard setiap pagi. Daftar "Guru Belum Isi Hari Ini" memberi tahu siapa yang perlu diingatkan sebelum hari berakhir.'],
                            ['notebook','Baca Detail Jurnal','Klik jurnal mana pun untuk melihat detail lengkap: materi ajar, jam masuk, keterlambatan, lampiran, dan catatan guru.'],
                            ['filter','Gunakan Filter','Gunakan filter nama guru, kelas, atau rentang tanggal untuk mencari jurnal tertentu dengan cepat.'],
                            ['alert-circle','Perhatikan Keterlambatan','Guru yang sering terlambat masuk kelas dapat diidentifikasi dari indikator merah di daftar jurnal.'],
                        ];
                        @endphp
                        @foreach($ksTips as $tip)
                        <div class="flex gap-3 p-4 rounded-xl border border-slate-200 dark:border-zinc-700">
                            <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950/40 flex items-center justify-center text-amber-500 flex-shrink-0">
                                <i data-lucide="{{ $tip[0] }}" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-700 dark:text-zinc-300 mb-1">{{ $tip[1] }}</p>
                                <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $tip[2] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endif
@endauth

</div>
@endsection
