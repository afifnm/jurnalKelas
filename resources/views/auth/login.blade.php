<!DOCTYPE html>
<html lang="id"
    x-data="{ dark: localStorage.getItem('dark') === 'true' }"
    :class="dark ? 'dark' : ''"
    class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Jurnal Kelas</title>
    <link rel="icon" type="image/webp" href="{{ asset('logo.webp') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 dark:bg-zinc-950 font-sans antialiased" style="font-family: 'Plus Jakarta Sans', sans-serif;">

<div class="min-h-screen flex">
    <!-- Left branding panel -->
    <div class="hidden lg:flex flex-col w-[45%] bg-gradient-to-br from-amber-400 via-amber-500 to-orange-500 p-12 relative overflow-hidden">
        <!-- Decorative circles -->
        <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-16 -left-16 w-64 h-64 rounded-full bg-white/10"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 rounded-full bg-white/5"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-12">
                <img src="{{ asset('logo.webp') }}" alt="Logo" class="w-12 h-12 object-contain drop-shadow-md">
                <span class="text-white font-bold text-lg">Jurnal Kelas</span>
            </div>

            <div class="mt-auto">
                <h1 class="text-4xl font-extrabold text-white leading-tight mb-4">
                    Sistem Jurnal<br>Mengajar Digital
                </h1>
                <p class="text-amber-100 text-lg leading-relaxed mb-8">
                    Pantau kepatuhan pengisian, ketepatan waktu, dan kinerja mengajar guru secara real-time.
                </p>

                <div class="flex flex-col gap-3">
                    <div class="flex items-center gap-3 text-white">
                        <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </div>
                        <span class="text-sm font-medium">Pengisian jurnal dari jadwal otomatis</span>
                    </div>
                    <div class="flex items-center gap-3 text-white">
                        <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </div>
                        <span class="text-sm font-medium">Validasi jurnal oleh Kepala Sekolah</span>
                    </div>
                    <div class="flex items-center gap-3 text-white">
                        <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </div>
                        <span class="text-sm font-medium">Laporan kinerja guru berbasis data</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right login form -->
    <div class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-md">

            <!-- Dark mode toggle -->
            <div class="flex justify-end mb-8">
                <button @click="dark = !dark; localStorage.setItem('dark', dark)"
                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm text-slate-500 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors">
                    <i data-lucide="sun" class="w-4 h-4" x-show="dark"></i>
                    <i data-lucide="moon" class="w-4 h-4" x-show="!dark"></i>
                    <span x-text="dark ? 'Light Mode' : 'Dark Mode'"></span>
                </button>
            </div>

            <!-- Mobile logo -->
            <div class="lg:hidden flex items-center gap-3 mb-8">
                <img src="{{ asset('logo.webp') }}" alt="Logo" class="w-12 h-12 object-contain">
                <div>
                    <p class="font-bold text-slate-800 dark:text-white">Jurnal Kelas</p>
                    <p class="text-xs text-slate-400 dark:text-zinc-500">Sistem Jurnal Mengajar Digital</p>
                </div>
            </div>

            <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-1.5">Masuk ke Akun</h2>
            <p class="text-slate-500 dark:text-zinc-400 text-sm mb-8">Gunakan username dan password yang diberikan administrator.</p>

            @if($errors->any())
            <div class="mb-6 flex items-start gap-3 px-4 py-3 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/40 rounded-xl">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5"></i>
                <div class="text-sm text-red-700 dark:text-red-400">
                    {{ $errors->first() }}
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="username" class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                        <i data-lucide="user" class="w-4 h-4 inline mr-1"></i>Username
                    </label>
                    <input type="text" id="username" name="username"
                           value="{{ old('username') }}"
                           class="input-field"
                           placeholder="Masukkan username"
                           autocomplete="username"
                           required autofocus>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
                        <i data-lucide="lock" class="w-4 h-4 inline mr-1"></i>Password
                    </label>
                    <div class="relative" x-data="{ showPass: false }">
                        <input :type="showPass ? 'text' : 'password'" id="password" name="password"
                               class="input-field pr-11"
                               placeholder="Masukkan password"
                               autocomplete="current-password"
                               required>
                        <button type="button" @click="showPass = !showPass"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-zinc-300 transition-colors">
                            <i :data-lucide="showPass ? 'eye-off' : 'eye'" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="remember" name="remember"
                           class="w-4 h-4 rounded border-slate-300 dark:border-zinc-600 text-amber-500 focus:ring-amber-400">
                    <label for="remember" class="text-sm text-slate-600 dark:text-zinc-400">Ingat saya</label>
                </div>

                <button type="submit"
                    class="w-full py-2.5 bg-amber-400 hover:bg-amber-500 text-zinc-900 font-bold text-sm rounded-xl shadow-sm hover:shadow transition-all duration-150 active:scale-[0.98] flex items-center justify-center gap-2">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    Masuk
                </button>
            </form>

            <p class="mt-8 text-center text-xs text-slate-400 dark:text-zinc-600">
                Jurnal Kelas &copy; {{ date('Y') }}. Hubungi administrator jika lupa password.
            </p>
        </div>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
    document.addEventListener('alpine:initialized', () => lucide.createIcons());
</script>
</body>
</html>
