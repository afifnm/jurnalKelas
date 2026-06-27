{{-- Panduan Penggunaan Sistem --}}
<div x-data="{ open: false, tab: 0 }"
     @open-guide.window="open = true; tab = 0"
     @keydown.escape.window="open = false"
     x-show="open"
     x-transition.opacity
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="display:none">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="open = false"></div>

    {{-- Modal --}}
    <div class="relative w-full max-w-2xl max-h-[90vh] flex flex-col bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-slate-200/80 dark:border-zinc-700/50"
         @click.stop>

        {{-- Header --}}
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-200/80 dark:border-zinc-700/50 flex-shrink-0">
            <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center text-amber-600 dark:text-amber-400">
                <i data-lucide="book-open" class="w-5 h-5"></i>
            </div>
            <div>
                <h2 class="text-base font-bold text-slate-800 dark:text-white">Panduan Penggunaan Sistem</h2>
                <p class="text-xs text-slate-400 dark:text-zinc-500">Jurnal Kelas — SMK Pemnas Sukoharjo</p>
            </div>
            <button @click="open = false" class="ml-auto text-slate-400 dark:text-zinc-500 hover:text-slate-600 dark:hover:text-zinc-300 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        @auth

        {{-- ============================================================ --}}
        {{-- ADMIN --}}
        {{-- ============================================================ --}}
        @if(auth()->user()->hasRole('admin'))

        {{-- Tab buttons --}}
        <div class="flex gap-1 px-6 pt-4 pb-0 flex-shrink-0 overflow-x-auto">
            @php $adminTabs = ['Wajib Setup Awal','Kelola Jadwal','Monitor Jurnal','Master Data']; @endphp
            @foreach($adminTabs as $i => $label)
            <button @click="tab = {{ $i }}"
                :class="tab === {{ $i }} ? 'bg-amber-500 text-white' : 'bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 hover:bg-slate-200 dark:hover:bg-zinc-700'"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors flex-shrink-0">
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- Scrollable body --}}
        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-3">

            {{-- Tab 0: Wajib Setup Awal --}}
            <div x-show="tab === 0">
                <div class="mb-4 flex items-start gap-3 p-3.5 bg-amber-50 dark:bg-amber-950/30 border border-amber-200/60 dark:border-amber-800/30 rounded-xl text-xs text-amber-800 dark:text-amber-300">
                    <i data-lucide="triangle-alert" class="w-4 h-4 flex-shrink-0 mt-0.5"></i>
                    <p><strong>Penting!</strong> Sebelum guru dapat mengisi jurnal, Admin <strong>wajib</strong> menyelesaikan 5 langkah setup berikut secara berurutan.</p>
                </div>

                {{-- Step 1 --}}
                <div class="flex gap-4 mb-3">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">1</div>
                        <div class="w-0.5 flex-1 bg-slate-200 dark:bg-zinc-700 mt-1"></div>
                    </div>
                    <div class="pb-4 flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <i data-lucide="calendar-range" class="w-4 h-4 text-amber-500"></i>
                            <p class="text-sm font-semibold text-slate-800 dark:text-white">Buat & Aktifkan Tahun Ajaran</p>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mb-2">Tahun ajaran adalah fondasi seluruh data. Semua jadwal dan jurnal terikat ke tahun ajaran yang aktif.</p>
                        <div class="bg-slate-50 dark:bg-zinc-800 rounded-lg p-3 text-xs text-slate-600 dark:text-zinc-400 space-y-1">
                            <p>1. Buka <strong>Master Data → Tahun Ajaran</strong> di sidebar</p>
                            <p>2. Klik <strong>"Tambah Tahun Ajaran"</strong>, isi nama (misal: 2024/2025) & semester</p>
                            <p>3. Klik tombol <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-400 rounded text-[10px] font-medium">Aktivasi</span> pada tahun ajaran yang baru dibuat</p>
                        </div>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="flex gap-4 mb-3">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">2</div>
                        <div class="w-0.5 flex-1 bg-slate-200 dark:bg-zinc-700 mt-1"></div>
                    </div>
                    <div class="pb-4 flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <i data-lucide="users" class="w-4 h-4 text-amber-500"></i>
                            <p class="text-sm font-semibold text-slate-800 dark:text-white">Tambah Pengguna Guru</p>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mb-2">Daftarkan semua guru yang akan menggunakan sistem, lalu bagikan kredensial login kepada mereka.</p>
                        <div class="bg-slate-50 dark:bg-zinc-800 rounded-lg p-3 text-xs text-slate-600 dark:text-zinc-400 space-y-1">
                            <p>1. Buka <strong>Master Data → Pengguna</strong></p>
                            <p>2. Klik <strong>"Tambah Pengguna"</strong> untuk tambah satu per satu, <em>atau</em></p>
                            <p>3. Gunakan <strong>"Import Excel"</strong> untuk menambah banyak guru sekaligus (unduh template terlebih dahulu)</p>
                            <p>4. Bagikan <strong>Username</strong> (kode guru) dan password default <span class="font-mono bg-slate-200 dark:bg-zinc-700 px-1 rounded">12345678</span> kepada setiap guru</p>
                        </div>
                        <div class="mt-2 flex items-start gap-2 p-2.5 bg-blue-50 dark:bg-blue-950/30 border border-blue-200/60 dark:border-blue-800/30 rounded-lg text-[11px] text-blue-700 dark:text-blue-400">
                            <i data-lucide="info" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5"></i>
                            <p>Ingatkan guru untuk mengingat username mereka. Username berupa kode guru yang Anda tentukan.</p>
                        </div>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="flex gap-4 mb-3">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">3</div>
                        <div class="w-0.5 flex-1 bg-slate-200 dark:bg-zinc-700 mt-1"></div>
                    </div>
                    <div class="pb-4 flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <i data-lucide="book-marked" class="w-4 h-4 text-amber-500"></i>
                            <p class="text-sm font-semibold text-slate-800 dark:text-white">Tambah Mata Pelajaran</p>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mb-2">Daftarkan semua mata pelajaran yang diajarkan di sekolah.</p>
                        <div class="bg-slate-50 dark:bg-zinc-800 rounded-lg p-3 text-xs text-slate-600 dark:text-zinc-400 space-y-1">
                            <p>1. Buka <strong>Master Data → Mata Pelajaran</strong></p>
                            <p>2. Klik <strong>"Tambah Mapel"</strong>, isi nama mata pelajaran</p>
                            <p>3. Ulangi untuk semua mapel yang ada</p>
                        </div>
                    </div>
                </div>

                {{-- Step 4 --}}
                <div class="flex gap-4 mb-3">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">4</div>
                        <div class="w-0.5 flex-1 bg-slate-200 dark:bg-zinc-700 mt-1"></div>
                    </div>
                    <div class="pb-4 flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <i data-lucide="school" class="w-4 h-4 text-amber-500"></i>
                            <p class="text-sm font-semibold text-slate-800 dark:text-white">Tambah Kelas</p>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mb-2">Daftarkan semua rombongan belajar (kelas) yang ada di sekolah.</p>
                        <div class="bg-slate-50 dark:bg-zinc-800 rounded-lg p-3 text-xs text-slate-600 dark:text-zinc-400 space-y-1">
                            <p>1. Buka <strong>Master Data → Kelas</strong></p>
                            <p>2. Klik <strong>"Tambah Kelas"</strong>, isi nama kelas (misal: X-A, XI-IPA, XII-TKJ)</p>
                            <p>3. Ulangi untuk semua kelas yang ada</p>
                        </div>
                    </div>
                </div>

                {{-- Step 5 --}}
                <div class="flex gap-4 mb-3">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">5</div>
                        <div class="w-0.5 flex-1 bg-slate-200 dark:bg-zinc-700 mt-1"></div>
                    </div>
                    <div class="pb-4 flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <i data-lucide="calendar-clock" class="w-4 h-4 text-amber-500"></i>
                            <p class="text-sm font-semibold text-slate-800 dark:text-white">Buat Jadwal Mengajar</p>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mb-2">Input jadwal mengajar untuk setiap guru. Jadwal bisa dikelola per kelas atau per guru.</p>
                        <div class="bg-slate-50 dark:bg-zinc-800 rounded-lg p-3 text-xs text-slate-600 dark:text-zinc-400 space-y-1">
                            <p>1. Buka menu <strong>Jadwal Kelas</strong></p>
                            <p>2. Pilih tampilan <strong>"Per Kelas"</strong> atau <strong>"Per Guru"</strong></p>
                            <p>3. Pilih kelas/guru dan klik <strong>"Tambah Jadwal"</strong></p>
                            <p>4. Isi: guru, mapel, hari, jam mulai, jam selesai — lalu simpan</p>
                            <p>5. Ulangi untuk semua sesi mengajar</p>
                        </div>
                        <div class="mt-2 flex items-start gap-2 p-2.5 bg-green-50 dark:bg-green-950/30 border border-green-200/60 dark:border-green-800/30 rounded-lg text-[11px] text-green-700 dark:text-green-400">
                            <i data-lucide="lightbulb" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5"></i>
                            <p>Untuk tahun ajaran baru, gunakan fitur <strong>Clone Jadwal</strong> di menu Tahun Ajaran untuk menyalin jadwal dari tahun sebelumnya — menghemat waktu input!</p>
                        </div>
                    </div>
                </div>

                {{-- Step 6: Done --}}
                <div class="flex gap-4">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center flex-shrink-0">
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="text-sm font-semibold text-green-600 dark:text-green-400">Sistem Siap Digunakan!</p>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">Guru sudah dapat login dan mulai mengisi jurnal mengajar sesuai jadwal yang telah dibuat.</p>
                    </div>
                </div>
            </div>

            {{-- Tab 1: Kelola Jadwal --}}
            <div x-show="tab === 1" class="space-y-3">
                <p class="text-xs text-slate-500 dark:text-zinc-400">Jadwal mengajar adalah referensi utama sistem. Pastikan jadwal selalu sesuai dengan kondisi nyata di sekolah.</p>

                <div class="rounded-xl border border-slate-200 dark:border-zinc-700 overflow-hidden">
                    <div class="bg-slate-50 dark:bg-zinc-800 px-4 py-2.5 border-b border-slate-200 dark:border-zinc-700 flex items-center gap-2">
                        <i data-lucide="layout-grid" class="w-4 h-4 text-amber-500"></i>
                        <p class="text-xs font-semibold text-slate-700 dark:text-zinc-300">Dua Tampilan Jadwal</p>
                    </div>
                    <div class="p-4 grid grid-cols-2 gap-3">
                        <div class="p-3 bg-amber-50 dark:bg-amber-950/20 rounded-lg border border-amber-200/60 dark:border-amber-800/30">
                            <p class="text-xs font-semibold text-amber-700 dark:text-amber-400 mb-1">Per Kelas</p>
                            <p class="text-[11px] text-slate-500 dark:text-zinc-400">Melihat & mengelola semua guru yang mengajar di satu kelas. Cocok untuk memastikan tidak ada jam bentrok dalam satu kelas.</p>
                        </div>
                        <div class="p-3 bg-blue-50 dark:bg-blue-950/20 rounded-lg border border-blue-200/60 dark:border-blue-800/30">
                            <p class="text-xs font-semibold text-blue-700 dark:text-blue-400 mb-1">Per Guru</p>
                            <p class="text-[11px] text-slate-500 dark:text-zinc-400">Melihat & mengelola semua kelas yang diajar satu guru. Cocok untuk memastikan beban guru tidak bentrok.</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 dark:border-zinc-700 overflow-hidden">
                    <div class="bg-slate-50 dark:bg-zinc-800 px-4 py-2.5 border-b border-slate-200 dark:border-zinc-700 flex items-center gap-2">
                        <i data-lucide="printer" class="w-4 h-4 text-amber-500"></i>
                        <p class="text-xs font-semibold text-slate-700 dark:text-zinc-300">Fitur Cetak Jadwal</p>
                    </div>
                    <div class="p-4 text-xs text-slate-600 dark:text-zinc-400 space-y-1">
                        <p>• <strong>Cetak Semua</strong> — cetak jadwal seluruh kelas dalam satu halaman</p>
                        <p>• <strong>Cetak per Kelas</strong> — cetak jadwal satu kelas tertentu</p>
                        <p>• <strong>Cetak per Guru</strong> — cetak jadwal satu guru tertentu</p>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 dark:border-zinc-700 overflow-hidden">
                    <div class="bg-slate-50 dark:bg-zinc-800 px-4 py-2.5 border-b border-slate-200 dark:border-zinc-700 flex items-center gap-2">
                        <i data-lucide="copy" class="w-4 h-4 text-amber-500"></i>
                        <p class="text-xs font-semibold text-slate-700 dark:text-zinc-300">Clone Jadwal (Tahun Ajaran Baru)</p>
                    </div>
                    <div class="p-4 text-xs text-slate-600 dark:text-zinc-400 space-y-1">
                        <p>Saat membuat tahun ajaran baru, gunakan fitur <strong>Clone Jadwal</strong> di menu <strong>Master Data → Tahun Ajaran</strong> untuk menyalin semua jadwal dari tahun ajaran sebelumnya secara otomatis.</p>
                    </div>
                </div>
            </div>

            {{-- Tab 2: Monitor Jurnal --}}
            <div x-show="tab === 2" class="space-y-3">
                <p class="text-xs text-slate-500 dark:text-zinc-400">Admin dapat memantau seluruh aktivitas pengisian jurnal secara real-time tanpa perlu melakukan input apapun.</p>

                <div class="rounded-xl border border-slate-200 dark:border-zinc-700 overflow-hidden">
                    <div class="bg-slate-50 dark:bg-zinc-800 px-4 py-2.5 border-b border-slate-200 dark:border-zinc-700 flex items-center gap-2">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 text-amber-500"></i>
                        <p class="text-xs font-semibold text-slate-700 dark:text-zinc-300">Dashboard Admin</p>
                    </div>
                    <div class="p-4 text-xs text-slate-600 dark:text-zinc-400 space-y-2">
                        <p>• <strong>Mengajar Sekarang</strong> — melihat guru yang sedang dalam jam mengajar saat ini</p>
                        <p>• <strong>Jurnal Hari Ini</strong> — jumlah jurnal yang sudah diisi hari ini</p>
                        <p>• <strong>Guru Belum Isi</strong> — daftar guru yang belum mengisi jurnal untuk jadwal hari ini</p>
                        <p>• <strong>Grafik Kinerja Guru</strong> — performa bulanan seluruh guru dalam bentuk grafik</p>
                        <p>• <strong>Aktivitas Terbaru</strong> — log semua aktivitas sistem</p>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 dark:border-zinc-700 overflow-hidden">
                    <div class="bg-slate-50 dark:bg-zinc-800 px-4 py-2.5 border-b border-slate-200 dark:border-zinc-700 flex items-center gap-2">
                        <i data-lucide="notebook-text" class="w-4 h-4 text-amber-500"></i>
                        <p class="text-xs font-semibold text-slate-700 dark:text-zinc-300">Lihat Jurnal Guru</p>
                    </div>
                    <div class="p-4 text-xs text-slate-600 dark:text-zinc-400 space-y-1">
                        <p>Buka menu <strong>Jurnal Guru</strong> untuk melihat semua jurnal yang telah diisi.</p>
                        <p class="mt-1">Filter tersedia berdasarkan: Guru, Kelas, Rentang Tanggal.</p>
                        <p class="mt-1">Klik baris jurnal untuk melihat detail lengkap termasuk foto lampiran.</p>
                    </div>
                </div>
            </div>

            {{-- Tab 3: Master Data --}}
            <div x-show="tab === 3" class="space-y-3">
                <p class="text-xs text-slate-500 dark:text-zinc-400">Master data adalah data referensi yang digunakan di seluruh sistem. Kelola dengan hati-hati karena perubahan akan berdampak ke semua fitur.</p>

                <div class="space-y-2">
                    @php
                    $masterItems = [
                        ['icon'=>'users','title'=>'Pengguna','desc'=>'Kelola akun admin, guru, dan kepala sekolah. Nonaktifkan akun guru yang sudah tidak mengajar (jangan hapus agar data jurnal tetap terjaga).'],
                        ['icon'=>'school','title'=>'Kelas','desc'=>'Daftar rombongan belajar. Nama kelas yang diubah akan otomatis terupdate di semua jadwal & jurnal terkait.'],
                        ['icon'=>'book-marked','title'=>'Mata Pelajaran','desc'=>'Daftar mata pelajaran. Pastikan nama mapel konsisten agar laporan kinerja mudah dibaca.'],
                        ['icon'=>'calendar-range','title'=>'Tahun Ajaran','desc'=>'Hanya boleh ada SATU tahun ajaran aktif dalam satu waktu. Untuk ganti semester/tahun: buat baru → aktifkan → (opsional) clone jadwal.'],
                        ['icon'=>'building-2','title'=>'Identitas Sekolah','desc'=>'Nama sekolah, NPSN, alamat. Muncul di header laporan cetak.'],
                    ];
                    @endphp
                    @foreach($masterItems as $item)
                    <div class="flex gap-3 p-3 rounded-xl border border-slate-200 dark:border-zinc-700">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-zinc-800 flex items-center justify-center text-slate-500 dark:text-zinc-400 flex-shrink-0">
                            <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-700 dark:text-zinc-300">{{ $item['title'] }}</p>
                            <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5">{{ $item['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- ============================================================ --}}
        {{-- GURU --}}
        {{-- ============================================================ --}}
        @elseif(auth()->user()->hasRole('guru'))

        <div class="flex gap-1 px-6 pt-4 pb-0 flex-shrink-0 overflow-x-auto">
            @php $guruTabs = ['Cara Isi Jurnal','Status Jurnal','Tips']; @endphp
            @foreach($guruTabs as $i => $label)
            <button @click="tab = {{ $i }}"
                :class="tab === {{ $i }} ? 'bg-amber-500 text-white' : 'bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 hover:bg-slate-200 dark:hover:bg-zinc-700'"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors flex-shrink-0">
                {{ $label }}
            </button>
            @endforeach
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-3">

            {{-- Tab 0: Cara Isi Jurnal --}}
            <div x-show="tab === 0">
                <div class="mb-4 flex items-start gap-3 p-3.5 bg-blue-50 dark:bg-blue-950/30 border border-blue-200/60 dark:border-blue-800/30 rounded-xl text-xs text-blue-800 dark:text-blue-300">
                    <i data-lucide="info" class="w-4 h-4 flex-shrink-0 mt-0.5"></i>
                    <p>Isi jurnal <strong>setiap kali selesai mengajar</strong>. Jurnal yang belum diisi akan mempengaruhi skor kepatuhan Anda.</p>
                </div>

                {{-- Steps --}}
                @php
                $steps = [
                    ['n'=>1,'icon'=>'layout-dashboard','title'=>'Cek Dashboard','desc'=>'Setelah login, lihat bagian <strong>Jadwal Hari Ini</strong> di dashboard. Di sana terlihat semua jadwal mengajar Anda hari ini beserta statusnya.'],
                    ['n'=>2,'icon'=>'clipboard-pen','title'=>'Klik Tombol "Isi"','desc'=>'Klik tombol <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-amber-100 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 rounded text-[10px] font-medium">Isi</span> pada jadwal yang ingin diisi. Form jurnal akan terbuka otomatis dengan kelas dan mapel sudah terisi.'],
                    ['n'=>3,'icon'=>'pencil-line','title'=>'Isi Data Jurnal','desc'=>'Lengkapi form: jam masuk aktual, materi pembelajaran, dan catatan tambahan (siswa tidak hadir, dll). Field dengan tanda <strong>*</strong> wajib diisi.'],
                    ['n'=>4,'icon'=>'image','title'=>'Upload Foto (Opsional)','desc'=>'Lampirkan foto bukti mengajar (papan tulis, aktivitas siswa, dll). Maksimal 5 foto, format JPG/PNG/WEBP, ukuran masing-masing maksimal 5MB.'],
                    ['n'=>5,'icon'=>'send','title'=>'Simpan & Submit','desc'=>'Klik <strong>"Simpan Draft"</strong> jika belum selesai, atau <strong>"Submit"</strong> jika sudah siap. Setelah disubmit, jurnal akan menunggu ditinjau oleh Kepala Sekolah.'],
                ];
                @endphp

                @foreach($steps as $idx => $step)
                <div class="flex gap-4 {{ $idx < count($steps)-1 ? 'mb-3' : '' }}">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">{{ $step['n'] }}</div>
                        @if($idx < count($steps)-1)
                        <div class="w-0.5 flex-1 bg-slate-200 dark:bg-zinc-700 mt-1"></div>
                        @endif
                    </div>
                    <div class="pb-4 flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <i data-lucide="{{ $step['icon'] }}" class="w-4 h-4 text-amber-500"></i>
                            <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $step['title'] }}</p>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">{!! $step['desc'] !!}</p>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Tab 1: Status Jurnal --}}
            <div x-show="tab === 1" class="space-y-2">
                <p class="text-xs text-slate-500 dark:text-zinc-400 mb-3">Setiap jurnal memiliki status yang menunjukkan posisinya dalam alur pengisian. Berikut penjelasan setiap status:</p>

                @php
                $statuses = [
                    ['color'=>'bg-slate-100 dark:bg-zinc-800','tc'=>'text-slate-600 dark:text-zinc-400','label'=>'Draft','icon'=>'file-text','desc'=>'Jurnal tersimpan tetapi belum disubmit. Masih bisa diedit atau dihapus.'],
                    ['color'=>'bg-blue-50 dark:bg-blue-950/30','tc'=>'text-blue-600 dark:text-blue-400','label'=>'Submitted','icon'=>'clock','desc'=>'Jurnal sudah disubmit dan menunggu ditinjau oleh Kepala Sekolah. Tidak bisa diedit saat ini.'],
                    ['color'=>'bg-green-50 dark:bg-green-950/30','tc'=>'text-green-600 dark:text-green-400','label'=>'Tervalidasi','icon'=>'check-circle','desc'=>'Jurnal sudah ditinjau dan diterima oleh Kepala Sekolah. Jurnal ini dihitung dalam skor kepatuhan Anda.'],
                    ['color'=>'bg-red-50 dark:bg-red-950/30','tc'=>'text-red-600 dark:text-red-400','label'=>'Perlu Revisi','icon'=>'alert-circle','desc'=>'Jurnal dikembalikan oleh Kepala Sekolah dan perlu diperbaiki. Baca catatan revisi, lakukan perbaikan, lalu submit ulang.'],
                ];
                @endphp

                @foreach($statuses as $s)
                <div class="flex gap-3 p-3.5 rounded-xl border border-slate-200 dark:border-zinc-700 {{ $s['color'] }}">
                    <i data-lucide="{{ $s['icon'] }}" class="w-4 h-4 {{ $s['tc'] }} flex-shrink-0 mt-0.5"></i>
                    <div>
                        <p class="text-xs font-semibold {{ $s['tc'] }}">{{ $s['label'] }}</p>
                        <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5">{{ $s['desc'] }}</p>
                    </div>
                </div>
                @endforeach

                <div class="mt-3 p-3.5 rounded-xl border border-amber-200/60 dark:border-amber-800/30 bg-amber-50 dark:bg-amber-950/30">
                    <p class="text-xs font-semibold text-amber-700 dark:text-amber-400 mb-2">Cara Hitung Skor Kinerja</p>
                    <div class="text-[11px] text-slate-600 dark:text-zinc-400 space-y-1">
                        <p>• <strong>Kepatuhan (50%)</strong> — berapa persen jadwal yang sudah diisi jurnal</p>
                        <p>• <strong>Ketepatan Waktu (30%)</strong> — berapa persen jurnal yang diisi tanpa terlambat masuk</p>
                        <p>• <strong>Tervalidasi (20%)</strong> — berapa persen jurnal yang sudah divalidasi KS</p>
                    </div>
                </div>
            </div>

            {{-- Tab 2: Tips Guru --}}
            <div x-show="tab === 2" class="space-y-2">
                @php
                $tips = [
                    ['icon'=>'zap','title'=>'Isi Sesegera Mungkin','desc'=>'Isi jurnal segera setelah mengajar agar detail materi masih segar. Jurnal bisa diisi untuk hari-hari sebelumnya jika terlewat.'],
                    ['icon'=>'camera','title'=>'Foto Adalah Bukti','desc'=>'Lampirkan foto papan tulis atau aktivitas belajar sebagai bukti mengajar. Ini memudahkan proses validasi KS.'],
                    ['icon'=>'edit-3','title'=>'Manfaatkan Draft','desc'=>'Simpan sebagai draft dulu jika belum lengkap. Draft bisa diedit kapan saja sebelum disubmit.'],
                    ['icon'=>'alert-triangle','title'=>'Perhatikan Jam Masuk','desc'=>'Isi jam masuk aktual sesuai kondisi nyata. Keterlambatan lebih dari jadwal akan tercatat dan mempengaruhi skor.'],
                    ['icon'=>'refresh-cw','title'=>'Segera Revisi','desc'=>'Jika jurnal dikembalikan untuk revisi, segera perbaiki dan submit ulang agar tidak menumpuk dan mempengaruhi skor.'],
                ];
                @endphp
                @foreach($tips as $tip)
                <div class="flex gap-3 p-3.5 rounded-xl border border-slate-200 dark:border-zinc-700">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-950/40 flex items-center justify-center text-amber-500 flex-shrink-0">
                        <i data-lucide="{{ $tip['icon'] }}" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-700 dark:text-zinc-300">{{ $tip['title'] }}</p>
                        <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5">{{ $tip['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

        </div>

        {{-- ============================================================ --}}
        {{-- KS (Kepala Sekolah) --}}
        {{-- ============================================================ --}}
        @elseif(auth()->user()->hasRole('ks'))

        <div class="flex gap-1 px-6 pt-4 pb-0 flex-shrink-0 overflow-x-auto">
            @php $ksTabs = ['Dashboard KS','Lihat Jurnal Guru','Tips']; @endphp
            @foreach($ksTabs as $i => $label)
            <button @click="tab = {{ $i }}"
                :class="tab === {{ $i }} ? 'bg-amber-500 text-white' : 'bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 hover:bg-slate-200 dark:hover:bg-zinc-700'"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors flex-shrink-0">
                {{ $label }}
            </button>
            @endforeach
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-3">

            {{-- Tab 0: Dashboard KS --}}
            <div x-show="tab === 0" class="space-y-3">
                <p class="text-xs text-slate-500 dark:text-zinc-400">Dashboard Kepala Sekolah menampilkan ringkasan kondisi pengisian jurnal dan kinerja seluruh guru secara real-time.</p>

                <div class="rounded-xl border border-slate-200 dark:border-zinc-700 overflow-hidden">
                    <div class="bg-slate-50 dark:bg-zinc-800 px-4 py-2.5 border-b border-slate-200 dark:border-zinc-700 flex items-center gap-2">
                        <i data-lucide="bar-chart-2" class="w-4 h-4 text-amber-500"></i>
                        <p class="text-xs font-semibold text-slate-700 dark:text-zinc-300">Kartu Statistik</p>
                    </div>
                    <div class="p-4 text-xs text-slate-600 dark:text-zinc-400 space-y-1.5">
                        <p>• <strong>Total Guru Aktif</strong> — jumlah guru yang terdaftar dan aktif</p>
                        <p>• <strong>Guru Belum Isi Hari Ini</strong> — jumlah guru yang belum mengisi jurnal untuk jadwal hari ini</p>
                        <p>• <strong>Rata-rata Kepatuhan Bulan Ini</strong> — persentase kepatuhan pengisian jurnal seluruh guru bulan berjalan</p>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 dark:border-zinc-700 overflow-hidden">
                    <div class="bg-slate-50 dark:bg-zinc-800 px-4 py-2.5 border-b border-slate-200 dark:border-zinc-700 flex items-center gap-2">
                        <i data-lucide="trending-up" class="w-4 h-4 text-amber-500"></i>
                        <p class="text-xs font-semibold text-slate-700 dark:text-zinc-300">Tren & Kinerja Guru</p>
                    </div>
                    <div class="p-4 text-xs text-slate-600 dark:text-zinc-400 space-y-1.5">
                        <p>• <strong>Grafik Tren 6 Bulan</strong> — tren kepatuhan pengisian jurnal dalam 6 bulan terakhir</p>
                        <p>• <strong>Tabel Kinerja Guru</strong> — performa setiap guru bulan ini: kepatuhan, keterlambatan, dan skor kinerja</p>
                        <p>• <strong>Guru Belum Isi Hari Ini</strong> — daftar nama guru yang jadwalnya ada hari ini namun belum diisi</p>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl border border-amber-200/60 dark:border-amber-800/30 bg-amber-50 dark:bg-amber-950/30">
                    <p class="text-xs font-semibold text-amber-700 dark:text-amber-400 mb-2">Cara Membaca Skor Kinerja</p>
                    <div class="text-[11px] text-slate-600 dark:text-zinc-400 space-y-1">
                        <p>• <span class="text-green-600 dark:text-green-400 font-semibold">Hijau (≥80)</span> — kinerja baik</p>
                        <p>• <span class="text-amber-600 dark:text-amber-400 font-semibold">Kuning (60–79)</span> — perlu perhatian</p>
                        <p>• <span class="text-red-600 dark:text-red-400 font-semibold">Merah (&lt;60)</span> — kinerja rendah, perlu tindakan</p>
                    </div>
                </div>
            </div>

            {{-- Tab 1: Lihat Jurnal Guru --}}
            <div x-show="tab === 1" class="space-y-3">
                <p class="text-xs text-slate-500 dark:text-zinc-400">Buka menu <strong>Jurnal Guru</strong> untuk melihat semua jurnal yang telah diisi oleh guru.</p>

                <div class="rounded-xl border border-slate-200 dark:border-zinc-700 overflow-hidden">
                    <div class="bg-slate-50 dark:bg-zinc-800 px-4 py-2.5 border-b border-slate-200 dark:border-zinc-700 flex items-center gap-2">
                        <i data-lucide="filter" class="w-4 h-4 text-amber-500"></i>
                        <p class="text-xs font-semibold text-slate-700 dark:text-zinc-300">Filter yang Tersedia</p>
                    </div>
                    <div class="p-4 text-xs text-slate-600 dark:text-zinc-400 space-y-1">
                        <p>• <strong>Nama Guru</strong> — filter jurnal milik guru tertentu</p>
                        <p>• <strong>Kelas</strong> — filter berdasarkan kelas yang diajar</p>
                        <p>• <strong>Status</strong> — filter: Menunggu / Tervalidasi / Perlu Revisi</p>
                        <p>• <strong>Rentang Tanggal</strong> — filter jurnal dalam periode tertentu</p>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 dark:border-zinc-700 overflow-hidden">
                    <div class="bg-slate-50 dark:bg-zinc-800 px-4 py-2.5 border-b border-slate-200 dark:border-zinc-700 flex items-center gap-2">
                        <i data-lucide="eye" class="w-4 h-4 text-amber-500"></i>
                        <p class="text-xs font-semibold text-slate-700 dark:text-zinc-300">Detail Jurnal</p>
                    </div>
                    <div class="p-4 text-xs text-slate-600 dark:text-zinc-400 space-y-1">
                        <p>Klik pada baris jurnal untuk melihat detail lengkap, termasuk:</p>
                        <p>• Materi dan metode pembelajaran</p>
                        <p>• Kendala dan tindak lanjut yang dilaporkan guru</p>
                        <p>• Foto lampiran bukti mengajar</p>
                        <p>• Informasi keterlambatan (jika ada)</p>
                    </div>
                </div>
            </div>

            {{-- Tab 2: Tips KS --}}
            <div x-show="tab === 2" class="space-y-2">
                @php
                $ksTips = [
                    ['icon'=>'calendar','title'=>'Pantau Setiap Hari','desc'=>'Cek dashboard setiap pagi untuk melihat guru mana yang belum mengisi jurnal untuk jadwal hari ini.'],
                    ['icon'=>'trending-up','title'=>'Pantau Tren Bulanan','desc'=>'Gunakan grafik tren 6 bulan untuk mengidentifikasi pola penurunan kepatuhan sebelum menjadi masalah besar.'],
                    ['icon'=>'alert-circle','title'=>'Perhatikan Warna Merah','desc'=>'Guru dengan skor kinerja merah (<60) perlu pembinaan segera. Periksa detail jurnal mereka untuk memahami penyebabnya.'],
                    ['icon'=>'users','title'=>'Bandingkan Antar Guru','desc'=>'Tabel kinerja guru memungkinkan perbandingan mudah. Jadikan data ini bahan evaluasi dalam rapat rutin.'],
                ];
                @endphp
                @foreach($ksTips as $tip)
                <div class="flex gap-3 p-3.5 rounded-xl border border-slate-200 dark:border-zinc-700">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-950/40 flex items-center justify-center text-amber-500 flex-shrink-0">
                        <i data-lucide="{{ $tip['icon'] }}" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-700 dark:text-zinc-300">{{ $tip['title'] }}</p>
                        <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5">{{ $tip['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

        </div>

        @endif
        @endauth

        {{-- Footer --}}
        <div class="px-6 py-3 border-t border-slate-200/80 dark:border-zinc-700/50 flex-shrink-0 flex items-center justify-between">
            <p class="text-[11px] text-slate-400 dark:text-zinc-600">Jurnal Kelas — SMK Pemnas Sukoharjo</p>
            <button @click="open = false"
                class="px-3 py-1.5 bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 text-slate-600 dark:text-zinc-400 rounded-lg text-xs font-medium transition-colors">
                Tutup
            </button>
        </div>

    </div>
</div>
