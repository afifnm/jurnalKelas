@extends('layouts.app')

@section('title', 'Panduan Penggunaan')
@section('page-title', 'Panduan Penggunaan Sistem')

@section('breadcrumb')
    <i data-lucide="home" class="w-3 h-3"></i><span>Beranda</span>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <span class="text-slate-700 dark:text-zinc-200 font-medium">Panduan Penggunaan</span>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

@auth

{{-- ADMIN --}}
@if(auth()->user()->hasRole('admin'))
<div class="card">
    <div class="px-6 py-4 border-b border-slate-200/80 dark:border-zinc-700/50 flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center">
            <i data-lucide="shield" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
        </div>
        <div>
            <h2 class="text-base font-bold text-slate-800 dark:text-white">Panduan Admin</h2>
            <p class="text-xs text-slate-400 dark:text-zinc-500">Langkah wajib penyiapan sistem</p>
        </div>
    </div>
    <div class="p-6 space-y-8">
        
        <!-- Langkah 1: Master Data -->
        <div>
            <div class="flex items-center gap-2 mb-3">
                <div class="w-6 h-6 rounded-full bg-amber-500 text-white flex items-center justify-center text-xs font-bold">1</div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Input Data Master</h3>
            </div>
            <p class="text-sm text-slate-500 dark:text-zinc-400 mb-4 ml-8">Sebelum memulai penjadwalan, pastikan seluruh data master telah diinput dengan benar. Ikuti urutan berikut:</p>
            
            <div class="ml-8 grid gap-3 sm:grid-cols-2">
                <div class="p-4 bg-slate-50 dark:bg-zinc-800 rounded-xl border border-slate-200 dark:border-zinc-700">
                    <h4 class="font-bold text-slate-700 dark:text-zinc-300 text-sm mb-1 flex items-center gap-1.5"><i data-lucide="users" class="w-4 h-4 text-amber-500"></i> 1. Pengguna (Guru)</h4>
                    <p class="text-xs text-slate-500 dark:text-zinc-400 mb-2">Masukkan semua data guru yang akan mengajar.</p>
                    <div class="bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-[10px] p-2 rounded-lg font-medium flex gap-1">
                        <i data-lucide="lightbulb" class="w-3.5 h-3.5 flex-shrink-0"></i>
                        <span>Tips: Gunakan fitur <strong>Import Excel</strong> agar input banyak guru bisa selesai dalam hitungan detik!</span>
                    </div>
                </div>
                <div class="p-4 bg-slate-50 dark:bg-zinc-800 rounded-xl border border-slate-200 dark:border-zinc-700">
                    <h4 class="font-bold text-slate-700 dark:text-zinc-300 text-sm mb-1 flex items-center gap-1.5"><i data-lucide="school" class="w-4 h-4 text-amber-500"></i> 2. Kelas</h4>
                    <p class="text-xs text-slate-500 dark:text-zinc-400 mb-2">Input semua daftar rombongan belajar (kelas).</p>
                    <div class="bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-[10px] p-2 rounded-lg font-medium flex gap-1">
                        <i data-lucide="lightbulb" class="w-3.5 h-3.5 flex-shrink-0"></i>
                        <span>Tips: Sama seperti pengguna, gunakan fitur <strong>Import Excel</strong> untuk mempercepat input kelas.</span>
                    </div>
                </div>
                <div class="p-4 bg-slate-50 dark:bg-zinc-800 rounded-xl border border-slate-200 dark:border-zinc-700">
                    <h4 class="font-bold text-slate-700 dark:text-zinc-300 text-sm mb-1 flex items-center gap-1.5"><i data-lucide="book-marked" class="w-4 h-4 text-amber-500"></i> 3. Mata Pelajaran</h4>
                    <p class="text-xs text-slate-500 dark:text-zinc-400 mb-2">Daftarkan semua mata pelajaran di sekolah.</p>
                    <div class="bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-[10px] p-2 rounded-lg font-medium flex gap-1">
                        <i data-lucide="lightbulb" class="w-3.5 h-3.5 flex-shrink-0"></i>
                        <span>Tips: Gunakan fitur <strong>Import Excel</strong> untuk menginput puluhan mata pelajaran sekaligus.</span>
                    </div>
                </div>
                <div class="p-4 bg-slate-50 dark:bg-zinc-800 rounded-xl border border-slate-200 dark:border-zinc-700">
                    <h4 class="font-bold text-slate-700 dark:text-zinc-300 text-sm mb-1 flex items-center gap-1.5"><i data-lucide="clock" class="w-4 h-4 text-amber-500"></i> 4. Jam Pelajaran</h4>
                    <p class="text-xs text-slate-500 dark:text-zinc-400 mb-2">Atur referensi waktu jam pelajaran.</p>
                    <div class="bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-[10px] p-2 rounded-lg font-medium flex gap-1">
                        <i data-lucide="copy" class="w-3.5 h-3.5 flex-shrink-0"></i>
                        <span>Tips: Gunakan fitur <strong>Clone</strong> ke hari lain (misal dari Senin disalin ke Selasa dst) agar tidak perlu menginput satu per satu!</span>
                    </div>
                </div>
                <div class="p-4 bg-slate-50 dark:bg-zinc-800 rounded-xl border border-slate-200 dark:border-zinc-700 sm:col-span-2">
                    <h4 class="font-bold text-slate-700 dark:text-zinc-300 text-sm mb-1 flex items-center gap-1.5"><i data-lucide="calendar-days" class="w-4 h-4 text-amber-500"></i> 5. Tahun Ajaran</h4>
                    <p class="text-xs text-slate-500 dark:text-zinc-400">Buat Tahun Ajaran baru dan pastikan statusnya <strong>Aktif</strong>. Hanya satu Tahun Ajaran yang boleh aktif dalam satu waktu.</p>
                </div>
            </div>
        </div>

        <hr class="border-slate-100 dark:border-zinc-800">

        <!-- Langkah 2: Penjadwalan -->
        <div>
            <div class="flex items-center gap-2 mb-3">
                <div class="w-6 h-6 rounded-full bg-amber-500 text-white flex items-center justify-center text-xs font-bold">2</div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white">Mapping Penjadwalan (Jadwal Mengajar)</h3>
            </div>
            <p class="text-sm text-slate-500 dark:text-zinc-400 mb-4 ml-8">Setelah Data Master siap, tahap selanjutnya adalah penjadwalan. Sistem ini menyediakan 5 cara mudah untuk memetakan (mapping) kelas:</p>
            
            <div class="ml-8 grid gap-4 sm:grid-cols-2">
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0"><i data-lucide="user" class="w-4 h-4"></i></div>
                    <div>
                        <h4 class="font-bold text-slate-700 dark:text-zinc-300 text-sm mb-1">1. Penjadwalan per Guru</h4>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">Pilih satu guru tertentu, lalu input di kelas apa saja dan hari apa saja guru tersebut mengajar.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-lg bg-teal-50 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 flex items-center justify-center flex-shrink-0"><i data-lucide="school" class="w-4 h-4"></i></div>
                    <div>
                        <h4 class="font-bold text-slate-700 dark:text-zinc-300 text-sm mb-1">2. Penjadwalan per Kelas</h4>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">Pilih satu kelas tertentu, lalu petakan guru siapa saja yang masuk ke kelas tersebut pada hari apa.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center flex-shrink-0"><i data-lucide="grip-horizontal" class="w-4 h-4"></i></div>
                    <div>
                        <h4 class="font-bold text-slate-700 dark:text-zinc-300 text-sm mb-1">3. Drag and Drop</h4>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">Cara paling visual dan cepat! Tinggal tarik (drag) kombinasi Guru & Mapel ke grid waktu/kelas yang tersedia.</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0"><i data-lucide="wand-2" class="w-4 h-4"></i></div>
                    <div>
                        <h4 class="font-bold text-slate-700 dark:text-zinc-300 text-sm mb-1">4. Generate Otomatis</h4>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mb-1">Gunakan mapping generate otomatis pada menu tahun ajaran untuk memetakan jadwal secara otomatis berdasarkan beban mengajar.</p>
                        <div class="bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-[10px] p-2 rounded-lg font-medium flex gap-1">
                            <i data-lucide="alert-triangle" class="w-3.5 h-3.5 flex-shrink-0"></i>
                            <span>Catatan: Wajib mengisi <strong>Pembagian Tugas</strong> terlebih dahulu jika ingin menggunakan fitur ini.</span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3 sm:col-span-2">
                    <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center flex-shrink-0"><i data-lucide="copy" class="w-4 h-4"></i></div>
                    <div>
                        <h4 class="font-bold text-slate-700 dark:text-zinc-300 text-sm mb-1">5. Clone Jadwal</h4>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">Salin jadwal dari tahun ajaran sebelumnya (jika ada) ke tahun ajaran yang sedang aktif.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- KEPALA SEKOLAH --}}
@elseif(auth()->user()->hasRole('ks'))
<div class="card">
    <div class="px-6 py-4 border-b border-slate-200/80 dark:border-zinc-700/50 flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center">
            <i data-lucide="star" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
        </div>
        <div>
            <h2 class="text-base font-bold text-slate-800 dark:text-white">Panduan Kepala Sekolah</h2>
            <p class="text-xs text-slate-400 dark:text-zinc-500">Fokus pada pemantauan (monitoring)</p>
        </div>
    </div>
    <div class="p-6 text-center py-12">
        <div class="w-20 h-20 bg-amber-50 dark:bg-amber-900/20 rounded-full flex items-center justify-center mx-auto mb-6">
            <i data-lucide="monitor-stop" class="w-10 h-10 text-amber-500 opacity-80"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-3">Monitoring Terpadu</h3>
        <p class="text-sm text-slate-500 dark:text-zinc-400 max-w-lg mx-auto mb-10 leading-relaxed">Sebagai Kepala Sekolah, Anda dapat fokus sepenuhnya pada <strong>monitoring, pengawasan, dan pelaporan</strong>. Sistem akan menyajikan data real-time dari aktivitas harian tanpa perlu melakukan pengaturan data master maupun penjadwalan manual.</p>
        
        <div class="grid sm:grid-cols-3 gap-5 text-left max-w-3xl mx-auto">
            <div class="p-5 rounded-2xl border border-slate-200 dark:border-zinc-700 bg-slate-50 dark:bg-zinc-800/80 hover:border-blue-300 dark:hover:border-blue-700 transition-colors">
                <i data-lucide="bar-chart-3" class="w-7 h-7 text-blue-500 mb-4"></i>
                <h4 class="font-bold text-slate-800 dark:text-zinc-200 text-sm mb-2">Dashboard Real-time</h4>
                <p class="text-xs text-slate-500 dark:text-zinc-400 leading-relaxed">Lihat rekapitulasi daftar guru yang mengajar hari ini serta guru yang belum mengisi jurnal secara langsung dari menu Dashboard.</p>
            </div>
            <div class="p-5 rounded-2xl border border-slate-200 dark:border-zinc-700 bg-slate-50 dark:bg-zinc-800/80 hover:border-green-300 dark:hover:border-green-700 transition-colors">
                <i data-lucide="file-check-2" class="w-7 h-7 text-green-500 mb-4"></i>
                <h4 class="font-bold text-slate-800 dark:text-zinc-200 text-sm mb-2">Cek Detail Jurnal</h4>
                <p class="text-xs text-slate-500 dark:text-zinc-400 leading-relaxed">Akses menu Jurnal Guru untuk membaca detail materi yang diajarkan beserta dokumentasi foto dan catatan tambahan dari guru.</p>
            </div>
            <div class="p-5 rounded-2xl border border-slate-200 dark:border-zinc-700 bg-slate-50 dark:bg-zinc-800/80 hover:border-amber-300 dark:hover:border-amber-700 transition-colors">
                <i data-lucide="calendar-search" class="w-7 h-7 text-amber-500 mb-4"></i>
                <h4 class="font-bold text-slate-800 dark:text-zinc-200 text-sm mb-2">Peninjauan Jadwal</h4>
                <p class="text-xs text-slate-500 dark:text-zinc-400 leading-relaxed">Pantau jadwal keseluruhan guru di sekolah (per guru maupun per kelas) untuk memantau sebaran jam mengajar dengan akurat.</p>
            </div>
        </div>
    </div>
</div>

{{-- GURU --}}
@elseif(auth()->user()->hasRole('guru'))
<div class="card">
    <div class="px-6 py-4 border-b border-slate-200/80 dark:border-zinc-700/50 flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center">
            <i data-lucide="user" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
        </div>
        <div>
            <h2 class="text-base font-bold text-slate-800 dark:text-white">Panduan Guru</h2>
            <p class="text-xs text-slate-400 dark:text-zinc-500">Fokus pada pengisian jurnal mengajar</p>
        </div>
    </div>
    <div class="p-6">
        <div class="flex items-center gap-3 mb-8 p-4 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200/50 dark:border-amber-800/30 text-amber-800 dark:text-amber-400 text-sm">
            <i data-lucide="info" class="w-5 h-5 flex-shrink-0"></i>
            <p>Sebagai guru, tugas utama Anda di sistem ini sangat praktis: <strong>pastikan setiap selesai sesi mengajar, Anda langsung mengisi jurnal harian.</strong></p>
        </div>

        <div class="relative pl-10 space-y-10 before:content-[''] before:absolute before:left-[19px] before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200 dark:before:bg-zinc-700">
            <div class="relative">
                <div class="absolute -left-[41px] top-0 w-10 h-10 rounded-full bg-amber-500 text-white flex items-center justify-center font-bold text-sm shadow ring-4 ring-white dark:ring-zinc-900">1</div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white mb-1.5">Cek Dashboard</h3>
                <p class="text-sm text-slate-500 dark:text-zinc-400 leading-relaxed">Saat login, Dashboard akan otomatis menampilkan <strong>Jadwal Mengajar Hari Ini</strong> milik Anda. Jika ada kelas yang belum diisi jurnalnya, segera klik tombol <span class="px-2.5 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-xs font-bold rounded-lg mx-1 inline-flex items-center gap-1"><i data-lucide="pencil" class="w-3 h-3"></i> ISI JURNAL</span> di sebelahnya.</p>
            </div>
            <div class="relative">
                <div class="absolute -left-[41px] top-0 w-10 h-10 rounded-full bg-amber-500 text-white flex items-center justify-center font-bold text-sm shadow ring-4 ring-white dark:ring-zinc-900">2</div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white mb-1.5">Isi Form Jurnal</h3>
                <p class="text-sm text-slate-500 dark:text-zinc-400 mb-3 leading-relaxed">Saat form isian terbuka, Anda hanya perlu melengkapi hal berikut:</p>
                <div class="grid sm:grid-cols-2 gap-3 mb-2">
                    <div class="bg-slate-50 dark:bg-zinc-800 p-3 rounded-xl border border-slate-100 dark:border-zinc-700">
                        <h4 class="font-semibold text-slate-700 dark:text-zinc-300 text-sm mb-1">Pilih Kelas & Mapel</h4>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">Pilih kelas dan mapel yang diajar. Ini bisa diisi <strong>otomatis</strong> dengan cara mengeklik jadwal yang ada di bagian atas form.</p>
                    </div>
                    <div class="bg-slate-50 dark:bg-zinc-800 p-3 rounded-xl border border-slate-100 dark:border-zinc-700">
                        <h4 class="font-semibold text-slate-700 dark:text-zinc-300 text-sm mb-1">Materi Pembelajaran</h4>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">Uraikan topik atau materi utama yang baru saja Anda sampaikan di kelas tersebut.</p>
                    </div>
                    <div class="bg-slate-50 dark:bg-zinc-800 p-3 rounded-xl border border-slate-100 dark:border-zinc-700 sm:col-span-2">
                        <h4 class="font-semibold text-slate-700 dark:text-zinc-300 text-sm mb-1">Catatan Tambahan</h4>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">Sebutkan daftar absen siswa (alpa/sakit/izin) atau informasi lain yang perlu dicatat saat kegiatan belajar mengajar.</p>
                    </div>
                </div>
            </div>
            <div class="relative">
                <div class="absolute -left-[41px] top-0 w-10 h-10 rounded-full bg-amber-500 text-white flex items-center justify-center font-bold text-sm shadow ring-4 ring-white dark:ring-zinc-900">3</div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white mb-1.5">Upload Bukti Foto <span class="text-xs font-normal text-slate-400 ml-1">(Opsional)</span></h3>
                <p class="text-sm text-slate-500 dark:text-zinc-400 leading-relaxed">Sertakan dokumentasi visual kegiatan kelas (foto presentasi, papan tulis, dsb.). Klik <strong>Simpan Jurnal</strong> dan selesai!</p>
            </div>
            <div class="relative">
                <div class="absolute -left-[41px] top-0 w-10 h-10 rounded-full bg-slate-200 dark:bg-zinc-700 text-slate-600 dark:text-zinc-300 flex items-center justify-center shadow ring-4 ring-white dark:ring-zinc-900"><i data-lucide="edit-3" class="w-5 h-5"></i></div>
                <h3 class="text-base font-bold text-slate-800 dark:text-white mb-1.5">Edit atau Hapus Jurnal</h3>
                <p class="text-sm text-slate-500 dark:text-zinc-400 leading-relaxed">Anda bisa meninjau ulang melalui menu <strong>Histori Jurnal</strong>. Bila terdapat kekeliruan (salah input materi atau salah kelas), Anda dapat memperbarui (edit) atau bahkan menghapusnya kapan saja.</p>
            </div>
        </div>
    </div>
</div>

@endif
@endauth

</div>
@endsection
